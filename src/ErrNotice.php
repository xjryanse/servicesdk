<?php
namespace xjryanse\servicesdk;

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
     * 当前 HTTP 请求的 URL、方法、参数（CLI 无请求上下文时为空）。
     * 参数为 GET+POST 合并；常见敏感键脱敏。
     *
     * @return array{url:string,method:string,params:array}
     */
    private static function requestSnapshot(){
        if(php_sapi_name() === 'cli'){
            return ['url' => '', 'method' => 'CLI', 'params' => []];
        }
        $uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '';
        if($uri === ''){
            return ['url' => '', 'method' => '', 'params' => []];
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? (string)$_SERVER['SERVER_NAME'] : '');
        $url = $host !== '' ? ($scheme.'://'.$host.$uri) : $uri;
        $method = isset($_SERVER['REQUEST_METHOD']) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $params = array_merge($_GET, $_POST);
        $params = self::sanitizeRequestParams($params);
        return ['url' => $url, 'method' => $method, 'params' => $params];
    }

    /**
     * 对 password/token 等键脱敏，避免告警里泄露凭证。
     */
    private static function sanitizeRequestParams(array $params){
        $maskKeys = ['password','pwd','passwd','pass','token','secret','authorization','cookie'];
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
     * V2 协议站点 ID（必须稳定且非空）。
     * 可通过环境变量显式指定：OPSBAO_SITE_ID。
     */
    private static function siteId(){
        $sid = trim((string)(getenv('OPSBAO_SITE_ID') ?: ''));
        if($sid !== ''){
            return $sid;
        }
        $server = self::serverName();
        $ip = self::localIp();
        return 'sid-'.substr(md5($server.'|'.$ip), 0, 12);
    }

    /**
     * V2 站点展示名（可选，便于 App 端展示）。
     */
    private static function siteName(){
        $name = trim((string)(getenv('OPSBAO_SITE_NAME') ?: ''));
        return $name !== '' ? $name : self::serverName();
    }

    /**
     * V2 来源短字段 s（可选）。
     */
    private static function siteSource(){
        $src = trim((string)(getenv('OPSBAO_SITE_SOURCE') ?: ''));
        return $src !== '' ? $src : ('service_logdb@'.self::localIp());
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

    /**
     * 通过 TCP 短连接推送一条消息：
     * 4 字节大端长度 + UTF-8 JSON。
     */
    private static function tcpPush($host, $port, array $payload){
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
        $runtime = trim((string)($context['runtime'] ?? ''));
        if($runtime === ''){
            $runtime = php_sapi_name() === 'cli' ? 'worker' : 'phpfpm';
        }
        $message = $e ? $e->getMessage() : '未知异常';
        $file = $e ? $e->getFile() : '';
        $line = $e ? $e->getLine() : '';
        $server = self::serverName();
        $ip = self::localIp();
        $req = self::requestSnapshot();

        $text = $message;
        $text .= "\n"."[主机]".$server." (".$ip.")";
        $text .= $file ? "\n[文件]".$file : '';
        $text .= $line ? "\n[行数]".$line : '';
        if($req['url'] !== ''){
            $text .= "\n[请求]".($req['method'] !== '' ? $req['method'].' ' : '').$req['url'];
        }
        $paramsLine = self::formatParamsLine($req['params']);
        if($paramsLine !== ''){
            $text .= "\n[参数]".$paramsLine;
        }
        $text .= "\n[运行时]".$runtime;

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
