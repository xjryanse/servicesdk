<?php
namespace xjryanse\servicesdk;

use xjryanse\servicesdk\comm\TcpRetry;

/**
 * 异常消息通知1
 */
class ErrNotice {
    /**
     * 进程内去重缓存：key = sid@ver
     * @var array<string,bool>
     */
    private static $siteDictSentMap = [];
    private static function serverName(){
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : php_uname('n');
    }

    private static function localIp(){
        if(class_exists(\xjryanse\phplite\logic\Network::class)){
            foreach(\xjryanse\phplite\logic\Network::serverIps() as $ip){
                $ip = trim((string)$ip);
                if($ip !== '' && strpos($ip, '10.') === 0){
                    return $ip;
                }
            }
        }
        if(!empty($_SERVER['SERVER_ADDR'])){
            return $_SERVER['SERVER_ADDR'];
        }
        $ip = @gethostbyname(gethostname());
        if($ip && $ip !== gethostname()){
            return $ip;
        }
        return 'unknown';
    }

    /**
     * 合并调用方 context 与进程内 err_notice_ctx（Worker 请求上下文）。
     */
    private static function mergeContext(array $context){
        if(!empty($GLOBALS['err_notice_ctx']) && is_array($GLOBALS['err_notice_ctx'])){
            return array_merge($GLOBALS['err_notice_ctx'], $context);
        }
        return $context;
    }

    /**
     * 当前 HTTP 请求的 URL、方法（CLI 无 HTTP 上下文时为空）。
     *
     * @return array{url:string,method:string}
     */
    private static function requestSnapshot(){
        if(php_sapi_name() === 'cli'){
            return ['url' => '', 'method' => 'CLI'];
        }
        $uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '';
        if($uri === ''){
            return ['url' => '', 'method' => ''];
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? (string)$_SERVER['SERVER_NAME'] : '');
        $url = $host !== '' ? ($scheme.'://'.$host.$uri) : $uri;
        $method = isset($_SERVER['REQUEST_METHOD']) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        return ['url' => $url, 'method' => $method];
    }

    /**
     * FPM 请求参数：优先已解析的 JSON Body（RqParams），再回退 GET/POST 与 php://input。
     */
    private static function fpmRequestParams(){
        $params = [];
        try {
            if(class_exists(\xjryanse\phplite\facade\Request::class)){
                $merged = \xjryanse\phplite\facade\Request::param();
                if(is_array($merged) && $merged !== []){
                    $params = $merged;
                }
            }
        } catch (\Throwable $ignore) {
        }
        if($params === []){
            $get = is_array($_GET) ? $_GET : [];
            $post = is_array($_POST) ? $_POST : [];
            $params = array_merge($get, $post);
        }
        if($params === [] && php_sapi_name() !== 'cli'){
            $raw = @file_get_contents('php://input');
            if(is_string($raw) && $raw !== ''){
                $decoded = json_decode($raw, true);
                if(is_array($decoded)){
                    $params = $decoded;
                }
            }
        }
        return self::sanitizeRequestParams($params);
    }

    /**
     * 客户端 IP（优先 Request facade，再回退常见代理头）。
     */
    private static function clientIp(){
        try {
            if(class_exists(\xjryanse\phplite\facade\Request::class)){
                $ip = trim((string)\xjryanse\phplite\facade\Request::ip());
                if($ip !== ''){
                    return $ip;
                }
            }
        } catch (\Throwable $ignore) {
        }
        foreach(['HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key){
            if(empty($_SERVER[$key])){
                continue;
            }
            $ip = (string)$_SERVER[$key];
            if($key === 'HTTP_X_FORWARDED_FOR'){
                $ip = trim(explode(',', $ip)[0]);
            }
            if($ip !== ''){
                return $ip;
            }
        }
        return '';
    }

    /**
     * FPM 业务路由 module/controller/action。
     */
    private static function fpmBusinessRoute(){
        try {
            if(class_exists(\xjryanse\phplite\facade\Route::class)){
                $module = (string)\xjryanse\phplite\facade\Route::module();
                $controller = (string)\xjryanse\phplite\facade\Route::controller();
                $action = (string)\xjryanse\phplite\facade\Route::action();
                if($module !== '' && $controller !== '' && $action !== ''){
                    return $module.'/'.$controller.'/'.$action;
                }
            }
        } catch (\Throwable $ignore) {
        }
        return '';
    }

    private static function resolveTraceId(array $context){
        $traceId = trim((string)($context['trace_id'] ?? ''));
        if($traceId === '' && !empty($GLOBALS['trace_id'])){
            $traceId = trim((string)$GLOBALS['trace_id']);
        }
        return $traceId;
    }

    private static function resolveRoute(array $context, $runtime){
        $route = trim((string)($context['url'] ?? ''));
        if($route === '' && $runtime === 'phpfpm'){
            $route = self::fpmBusinessRoute();
        }
        return $route;
    }

    private static function resolveRequestParams(array $context, $runtime){
        if(isset($context['param']) && is_array($context['param']) && $context['param'] !== []){
            return self::sanitizeRequestParams($context['param']);
        }
        if($runtime === 'phpfpm'){
            return self::fpmRequestParams();
        }
        return [];
    }

    private static function sessionId(){
        global $sessionId;
        if(!empty($sessionId)){
            return (string)$sessionId;
        }
        if(session_status() === PHP_SESSION_ACTIVE){
            return (string)session_id();
        }
        return '';
    }

    private static function formatExceptionChain(\Throwable $e, $maxDepth = 3){
        $lines = [];
        $cur = $e->getPrevious();
        $depth = 0;
        while($cur && $depth < $maxDepth){
            $lines[] = '[Caused by] '.get_class($cur).': '.$cur->getMessage()
                .' @ '.$cur->getFile().':'.$cur->getLine();
            $cur = $cur->getPrevious();
            $depth++;
        }
        return implode("\n", $lines);
    }

    private static function formatTrace(\Throwable $e, $maxLen = 4096){
        $trace = $e->getTraceAsString();
        if(strlen($trace) > $maxLen){
            return substr($trace, 0, $maxLen).'...(truncated)';
        }
        return $trace;
    }

    private static function appendLine($text, $label, $value){
        $value = trim((string)$value);
        if($value === ''){
            return $text;
        }
        return $text."\n".'['.$label.']'.$value;
    }

    /**
     * 对 password/token 等键脱敏，避免告警里泄露凭证。
     */
    private static function sanitizeRequestParams(array $params){
        $maskKeys = [
            'password','pwd','passwd','pass','token','secret','authorization','cookie',
            'api_key','apikey','access_key','private_key','sessionid','session_id',
        ];
        $out = [];
        foreach($params as $k => $v){
            $key = is_string($k) ? strtolower($k) : $k;
            if(is_string($key) && in_array($key, $maskKeys, true)){
                $out[$k] = '***';
                continue;
            }
            if(is_array($v)){
                $out[$k] = self::sanitizeRequestParams($v);
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    /**
     * 将参数格式化为告警正文一行（过长截断）。
     */
    private static function formatParamsLine(array $params, $maxLen = 2000){
        if($params === []){
            return '';
        }
        $s = json_encode($params, JSON_UNESCAPED_UNICODE);
        if($s === false){
            return '';
        }
        if(strlen($s) > $maxLen){
            return substr($s, 0, $maxLen).'...(truncated)';
        }
        return $s;
    }

    /**
     * 运维宝 TCP 推送主机（短连接）。
     */
    private static function pushHost(){
        return getenv('OPSBAO_PUSH_HOST') ?: '47.119.159.229';
    }

    /**
     * 运维宝 TCP 推送端口（短连接）。
     */
    private static function pushPort(){
        $port = (int)(getenv('OPSBAO_PUSH_PORT') ?: 18003);
        return $port > 0 ? $port : 18003;
    }

    /**
     * 可选鉴权 token（对应 tools_server 的 TOOLS_SERVER_INGEST_TOKEN）。
     */
    private static function pushToken(){
        return getenv('OPSBAO_PUSH_TOKEN') ?: '';
    }

    /**
     * 项目根目录名（零配置）：如 /www/wwwroot/service_entry → service_entry。
     */
    private static function projectRootName(){
        if(defined('ROOT_PATH') && ROOT_PATH !== ''){
            $base = basename(rtrim(ROOT_PATH, '/\\'));
            if($base !== ''){
                return $base;
            }
        }
        if(!empty($_SERVER['DOCUMENT_ROOT'])){
            $parent = basename(dirname(rtrim((string)$_SERVER['DOCUMENT_ROOT'], '/\\')));
            if($parent !== ''){
                return $parent;
            }
        }
        return 'unknown';
    }

    /**
     * V2 协议站点 ID：按「项目根目录名 + 主机 IP」生成，同一库在不同域名下 sid 一致。
     */
    private static function siteId(){
        $ip = self::localIp();
        return 'sid-'.substr(md5(self::projectRootName().'|'.$ip), 0, 12);
    }

    /** V2 站点展示名：与项目根目录名一致。 */
    private static function siteName(){
        return self::projectRootName();
    }

    /** V2 来源短字段 s；notice 会追加 |phpfpm|worker 等运行时后缀。 */
    private static function siteSource(){
        return self::projectRootName();
    }

    private static function pushToOpsBao(array $payload){
        $host = self::pushHost();
        $port = self::pushPort();
        $token = self::pushToken();
        if($token){
            // tools_server 的 TCP ingest 支持在 JSON 中带 token 鉴权
            $payload['token'] = $token;
        }
        return self::tcpPush($host, $port, $payload);
    }

    /**
     * 发送 V2 站点字典（控制消息）。
     * 建议在服务启动、部署后或站点信息变更时调用一次。
     *
     * @param int|null $ver 字典版本号（递增）；为空时使用当前时间戳
     * @return array
     */
    public static function sendSiteDict($ver = null){
        $sid = self::siteId();
        $name = self::siteName();
        $source = self::siteSource();
        if($ver === null){
            $ver = time();
        }
        $dedupKey = $sid.'@'.(string)$ver;
        if(isset(self::$siteDictSentMap[$dedupKey])){
            return ['ok' => true, 'skipped' => true, 'reason' => 'already_sent_in_process', 'key' => $dedupKey];
        }

        $payload = [
            'tp'    => 'site_dict',
            'ver'   => (int)$ver,
            'sites' => [
                $sid => [
                    'n' => $name,
                    's' => $source
                ]
            ]
        ];

        $res = self::pushToOpsBao($payload);
        if(is_array($res) && isset($res['ok']) && $res['ok']){
            self::$siteDictSentMap[$dedupKey] = true;
        }
        return $res;
    }

    /** 首次告警前推送 site_dict，便于运维平台展示项目名与来源。 */
    private static function ensureSiteDictOnce(){
        static $done = false;
        if($done){
            return;
        }
        $done = true;
        try {
            self::sendSiteDict();
        } catch (\Throwable $ignore) {
        }
    }

    /**
     * 通过 TCP 短连接推送一条消息（含连接/发送失败重试，与 TcpRetry 环境变量一致）：
     * 4 字节大端长度 + UTF-8 JSON。
     */
    private static function tcpPush($host, $port, array $payload){
        $max = TcpRetry::maxAttempts();
        $delayMs = TcpRetry::delayMsBetweenAttempts();
        $last = null;
        for($i = 0; $i < $max; $i++){
            $last = self::tcpPushOnce($host, $port, $payload);
            if(!is_array($last)){
                return $last;
            }
            $err = $last['error'] ?? null;
            if(($err === 'connect_failed' || $err === 'send_failed') && $i + 1 < $max){
                if($delayMs > 0){
                    usleep($delayMs * 1000);
                }
                continue;
            }
            return $last;
        }
        return $last;
    }

    /**
     * 单次 TCP 推送（不重试）。
     */
    private static function tcpPushOnce($host, $port, array $payload){
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if($json === false){
            return ['ok' => false, 'error' => 'json_encode_failed'];
        }

        $body = $json;
        $frame = pack('N', strlen($body)) . $body;
        $errno = 0;
        $errstr = '';

        $fp = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            3
        );
        if(!$fp){
            return ['ok' => false, 'error' => 'connect_failed', 'errno' => $errno, 'errstr' => $errstr];
        }

        stream_set_timeout($fp, 3);

        $written = 0;
        $total = strlen($frame);
        while($written < $total){
            $n = fwrite($fp, substr($frame, $written));
            if($n === false || $n === 0){
                fclose($fp);
                return ['ok' => false, 'error' => 'send_failed'];
            }
            $written += $n;
        }

        // 读取服务端 ACK（若服务端开启 TOOLS_SERVER_TCP_INGEST_REPLY_ACK=1）。
        $hdr = fread($fp, 4);
        if($hdr !== false && strlen($hdr) === 4){
            $arr = unpack('Nlen', $hdr);
            $ackLen = (int)$arr['len'];
            if($ackLen > 0 && $ackLen < 1024 * 1024){
                $ackBody = '';
                while(strlen($ackBody) < $ackLen){
                    $chunk = fread($fp, $ackLen - strlen($ackBody));
                    if($chunk === false || $chunk === ''){
                        break;
                    }
                    $ackBody .= $chunk;
                }
                fclose($fp);
                $ackJson = json_decode($ackBody, true);
                if(is_array($ackJson)){
                    return $ackJson;
                }
                return ['ok' => true, 'ack_raw' => $ackBody];
            }
        }

        fclose($fp);
        return ['ok' => true, 'sent' => true];
    }

    public static function notice($e = null, array $context = []){
        self::ensureSiteDictOnce();

        $context = self::mergeContext($context);
        $runtime = trim((string)($context['runtime'] ?? ''));
        if($runtime === ''){
            $runtime = php_sapi_name() === 'cli' ? 'worker' : 'phpfpm';
        }

        $message = $e ? $e->getMessage() : '未知异常';
        $file = $e ? $e->getFile() : '';
        $line = $e ? $e->getLine() : '';
        $exClass = $e ? get_class($e) : '';
        $exCode = $e ? (string)$e->getCode() : '';
        $server = self::serverName();
        $ip = self::localIp();
        $req = self::requestSnapshot();
        $traceId = self::resolveTraceId($context);
        $route = self::resolveRoute($context, $runtime);
        $params = self::resolveRequestParams($context, $runtime);
        $clientIp = self::clientIp();
        $sessionId = self::sessionId();

        $text = $message;
        $text = self::appendLine($text, '类型', $exClass);
        if($exCode !== '' && $exCode !== '0'){
            $text = self::appendLine($text, '错误码', $exCode);
        }
        $text = self::appendLine($text, '主机', $server.' ('.$ip.')');
        $text = self::appendLine($text, '文件', $file);
        $text = self::appendLine($text, '行数', $line !== '' ? (string)$line : '');
        $text = self::appendLine($text, 'TraceId', $traceId);
        $text = self::appendLine($text, '调用方', trim((string)($context['caller_service'] ?? '')));
        $text = self::appendLine($text, '调用路由', trim((string)($context['caller_route'] ?? '')));
        $text = self::appendLine($text, '调用位置', trim((string)($context['caller_from'] ?? '')));
        $text = self::appendLine($text, '调用方IP', trim((string)($context['caller_ip'] ?? '')));
        $text = self::appendLine($text, '对端IP', trim((string)($context['caller_peer_ip'] ?? '')));
        $text = self::appendLine($text, '调用方运行时', trim((string)($context['caller_runtime'] ?? '')));
        if($route !== ''){
            $text = self::appendLine($text, '路由', $route);
        } elseif($req['url'] !== ''){
            $text = self::appendLine($text, '请求', ($req['method'] !== '' ? $req['method'].' ' : '').$req['url']);
        }
        $paramsLine = self::formatParamsLine($params);
        if($paramsLine !== ''){
            $text = self::appendLine($text, '参数', $paramsLine);
        }
        $text = self::appendLine($text, '客户端IP', $clientIp);
        $text = self::appendLine($text, 'Session', $sessionId);
        $text = self::appendLine($text, '运行时', $runtime);

        $env = trim((string)(getenv('APP_ENV') ?: getenv('RUNTIME_ENV') ?: ''));
        if($env !== ''){
            $text = self::appendLine($text, '环境', $env);
        }
        $text = self::appendLine($text, 'PHP', PHP_VERSION);

        if($e){
            $chain = self::formatExceptionChain($e);
            if($chain !== ''){
                $text = self::appendLine($text, '异常链', $chain);
            }
            $trace = self::formatTrace($e);
            if($trace !== ''){
                $text = self::appendLine($text, '堆栈', $trace);
            }
        }

        if(!empty($GLOBALS['serviceTraceArr']) && is_array($GLOBALS['serviceTraceArr'])){
            $serviceLine = self::formatParamsLine($GLOBALS['serviceTraceArr'], 1500);
            if($serviceLine !== ''){
                $text = self::appendLine($text, '微服务调用', $serviceLine);
            }
        }

        $eventId = 'err-'.md5($server.'|'.$message.'|'.$file.'|'.$line.'|'.date('YmdHi'));

        $payload = [
            // V2 协议核心字段
            'tp'         => 'event',
            'id'         => $eventId,
            'sid'        => self::siteId(),
            't'          => (int)(microtime(true) * 1000),
            // 约定：0=调试 1=信息 2=警告 3=错误 4=严重
            'sv'         => 3,
            'ttl'        => '业务异常告警['.$runtime.']',
            'm'          => $text,
            's'          => self::siteSource().'|'.$runtime
        ];
        if($traceId !== ''){
            $payload['trace_id'] = $traceId;
        }
        $callerService = trim((string)($context['caller_service'] ?? ''));
        $callerRoute = trim((string)($context['caller_route'] ?? ''));
        if($callerService !== ''){
            $payload['caller_service'] = $callerService;
        }
        if($callerRoute !== ''){
            $payload['caller_route'] = $callerRoute;
        }
        if($route !== ''){
            $payload['route'] = $route;
        }
        if($exClass !== ''){
            $payload['ex'] = $exClass;
        }
        if($exCode !== '' && $exCode !== '0'){
            $payload['ex_code'] = $exCode;
        }
        if($file !== ''){
            $payload['file'] = $file;
        }
        if($line !== ''){
            $payload['line'] = (int)$line;
        }

        return self::pushToOpsBao($payload);
    }
    /**
     * 队列堵塞通知
     */
    public static function msgqJamNotice($queueName, $count){
        $server = self::serverName();
        $ip = self::localIp();
        $text = $server."队列堵塞:";
        $text.= "\n"."[主机]".$server." (".$ip.")";
        $text.= "\n队列名称：".$queueName;
        $text.= "\n当前数量：".$count;

        $payload = [
            // V2 协议核心字段
            'tp'         => 'event',
            'id'         => 'queue-jam-'.md5($server.'|'.$queueName.'|'.date('YmdHi')),
            'sid'        => self::siteId(),
            't'          => (int)(microtime(true) * 1000),
            // 约定：0=调试 1=信息 2=警告 3=错误 4=严重
            'sv'         => 2,
            'ttl'        => '队列堵塞告警',
            'm'          => $text
        ];

        return self::pushToOpsBao($payload);
    }
}
