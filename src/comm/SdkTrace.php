<?php

namespace xjryanse\servicesdk\comm;

use xjryanse\phplite\logic\LogBuffer;
use xjryanse\servicesdk\exception\SdkCallException;

/**
 * 跨服务 trace：span 写入、message 解包、异常 trace 组装。
 */
class SdkTrace
{
    /**
     * 调试阶段原样保留入参（含 password）；上线后由网关/配置关闭 trace 输出。
     *
     * @param array<string,mixed> $params
     * @return array<string,mixed>
     */
    public static function sanitize(array $params): array
    {
        return $params;
    }

    /**
     * 从 legacy SDK 拼接 message 中提取最内层业务文案。
     */
    public static function unwrapUserMessage($msg): string
    {
        $msg = trim((string) $msg);
        if ($msg === '') {
            return '请求失败';
        }
        if (strpos($msg, '://') === false
            && strpos($msg, '接口异常') === false
            && strpos($msg, '接口无值') === false
            && strpos($msg, '请求参数') === false
            && strpos($msg, '无数据:') === false
        ) {
            return $msg;
        }
        if (preg_match('/内容:([^请求参数]+?)(?:请求参数|$)/u', $msg, $m)) {
            $inner = trim($m[1]);
            if ($inner === '') {
                return '请求失败';
            }
            if (strpos($inner, '内容:') !== false || strpos($inner, '://') !== false) {
                return self::unwrapUserMessage($inner);
            }
            return $inner;
        }
        return '请求失败';
    }

    public static function userMessageFromThrowable(\Throwable $e): string
    {
        if ($e instanceof SdkCallException && $e->userMessage !== '') {
            return $e->userMessage;
        }
        return self::unwrapUserMessage($e->getMessage());
    }

    /**
     * @param array<string,mixed> $resp
     * @return array<string,mixed>|null
     */
    public static function extractChildTraceFromResponse(array $resp)
    {
        if (!empty($resp['trace']) && is_array($resp['trace'])) {
            return $resp['trace'];
        }
        $spans = [];
        if (!empty($resp['$dev']['serviceArr']) && is_array($resp['$dev']['serviceArr'])) {
            $spans = self::spansFromServiceArr($resp['$dev']['serviceArr']);
        }
        if ($spans === []) {
            return null;
        }
        return [
            'root'  => '',
            'spans' => $spans,
            'error' => [
                'user_message' => self::unwrapUserMessage(isset($resp['message']) ? $resp['message'] : ''),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $resp
     */
    public static function mergeServiceArrIntoGlobal(array $resp): void
    {
        if (empty($resp['$dev']['serviceArr']) || !is_array($resp['$dev']['serviceArr'])) {
            return;
        }
        $spans = $resp['$dev']['serviceArr'];
        if (class_exists(\xjryanse\phplite\service\WorkerRequest::class)) {
            $wr = \xjryanse\phplite\service\WorkerRequest::current();
            if ($wr !== null) {
                $wr->mergeServiceSpans($spans);
                return;
            }
        }
        global $serviceTraceArr;
        $serviceTraceArr = !empty($serviceTraceArr) && is_array($serviceTraceArr)
            ? array_merge($serviceTraceArr, $spans)
            : $spans;
    }

    /**
     * @param array<int,array<string,mixed>> $serviceArr
     * @return array<int,array<string,mixed>>
     */
    public static function spansFromServiceArr(array $serviceArr): array
    {
        $spans = [];
        foreach ($serviceArr as $item) {
            if (!is_array($item)) {
                continue;
            }
            $request = isset($item['request']) ? $item['request'] : [];
            if (is_string($request)) {
                $decoded = json_decode($request, true);
                $request = is_array($decoded) ? $decoded : [];
            }
            $spans[] = [
                'seq'        => isset($item['call_seq']) ? $item['call_seq'] : 0,
                'status'     => isset($item['status']) ? $item['status'] : 'ok',
                'transport'  => isset($item['transport']) ? $item['transport'] : (isset($item['queryType']) ? $item['queryType'] : ''),
                'caller'     => isset($item['caller_service']) ? $item['caller_service'] : '',
                'route'      => isset($item['callee_route']) ? $item['callee_route'] : '',
                'host'       => isset($item['host']) ? $item['host'] : (isset($item['url']) ? $item['url'] : ''),
                'micro_diff' => isset($item['micro_diff']) ? $item['micro_diff'] : 0,
                'request'    => $request,
                'response'   => isset($item['response']) ? $item['response'] : '',
                'error_message' => isset($item['error_message']) ? $item['error_message'] : '',
            ];
        }
        return $spans;
    }

    /**
     * @param array<int,array<string,mixed>> $localServiceArr
     * @return array<string,mixed>
     */
    public static function buildErrorTrace(\Throwable $e, array $localServiceArr = []): array
    {
        $spans = self::spansFromServiceArr($localServiceArr);
        if ($e instanceof SdkCallException) {
            if ($spans === [] && !empty($e->span)) {
                $spans[] = self::spanView($e->span);
            }
            if (!empty($e->childTrace['spans']) && is_array($e->childTrace['spans'])) {
                $spans = array_merge($spans, $e->childTrace['spans']);
            }
        }
        $userMsg = self::userMessageFromThrowable($e);
        $trace = [
            'root'  => OutboundLogUtil::callerServiceLabel(),
            'spans' => $spans,
            'error' => [
                'type'         => $e instanceof SdkCallException ? 'sdk_call' : 'business',
                'user_message' => $userMsg,
            ],
        ];
        if ($e instanceof SdkCallException && $e->rawMessage !== '' && $e->rawMessage !== $userMsg) {
            $trace['error']['raw_message'] = $e->rawMessage;
        }
        return $trace;
    }

    /**
     * @param array<string,mixed> $span
     * @return array<string,mixed>
     */
    public static function spanView(array $span): array
    {
        $request = isset($span['request']) ? $span['request'] : [];
        if (is_string($request)) {
            $decoded = json_decode($request, true);
            $request = is_array($decoded) ? $decoded : [];
        }
        return [
            'seq'           => isset($span['call_seq']) ? $span['call_seq'] : 0,
            'status'        => isset($span['status']) ? $span['status'] : 'ok',
            'transport'     => isset($span['transport']) ? $span['transport'] : '',
            'caller'        => isset($span['caller_service']) ? $span['caller_service'] : '',
            'route'         => isset($span['callee_route']) ? $span['callee_route'] : '',
            'host'          => isset($span['host']) ? $span['host'] : (isset($span['url']) ? $span['url'] : ''),
            'micro_diff'    => isset($span['micro_diff']) ? $span['micro_diff'] : 0,
            'request'       => $request,
            'response'      => isset($span['response']) ? $span['response'] : '',
            'error_message' => isset($span['error_message']) ? $span['error_message'] : '',
        ];
    }

    /**
     * @param array<string,mixed> $span legacy serviceArr 条目
     */
    public static function pushSpan(array $span): void
    {
        if (class_exists(\xjryanse\phplite\service\WorkerRequest::class)) {
            $wr = \xjryanse\phplite\service\WorkerRequest::current();
            if ($wr !== null) {
                $wr->addServiceSpan($span);
                LogBuffer::push($span);
                return;
            }
        }
        global $serviceTraceArr;
        if (!isset($serviceTraceArr) || !is_array($serviceTraceArr)) {
            $serviceTraceArr = [];
        }
        $serviceTraceArr[] = $span;
        LogBuffer::push($span);
    }

    /**
     * @param array<string,mixed>|null $response
     * @return array<string,mixed>
     */
    public static function buildWorkerSpan(
        $host,
        $port,
        $route,
        array $param,
        $response,
        $startMTs,
        $endMTs,
        $status = 'ok',
        $errorMessage = ''
    ): array {
        $callerSvc = OutboundLogUtil::callerServiceLabel();
        $urlStr = $host . ':' . $port . '/' . $route;
        $safeParam = self::sanitize($param);
        $respSnippet = '';
        if (is_array($response)) {
            $respSnippet = mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 500) . '……';
        }
        return [
            'trace_id'       => self::currentTraceId(),
            'call_seq'       => OutboundLogUtil::nextSeq(),
            'call_direction' => 'outbound',
            'transport'      => 'workerman',
            'status'         => $status,
            'caller_service' => $callerSvc,
            'caller_from'    => OutboundLogUtil::callerFrame(),
            'callee_host'    => (string) $host,
            'callee_port'    => (string) $port,
            'callee_route'   => $route,
            'env'            => getenv('APP_ENV') !== false && getenv('APP_ENV') !== '' ? getenv('APP_ENV') : 'prod',
            'service_name'   => $callerSvc,
            'url'            => $urlStr,
            'micro_diff'     => $endMTs - $startMTs,
            'queryType'      => 'workerman',
            'host'           => $host . ':' . $port,
            'sourceHostName' => gethostname(),
            'request'        => json_encode($safeParam, JSON_UNESCAPED_UNICODE),
            'response'       => $respSnippet,
            'error_message'  => (string) $errorMessage,
            'create_time'    => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param array<string,mixed>|null $response
     * @return array<string,mixed>
     */
    public static function buildHttpSpan(
        $url,
        array $request,
        $response,
        $startMTs,
        $endMTs,
        $status = 'ok',
        $errorMessage = ''
    ): array {
        $callerSvc = OutboundLogUtil::callerServiceLabel();
        $parts = parse_url($url);
        $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'http';
        $calleeHost = isset($parts['host']) ? $parts['host'] : '';
        $calleePort = isset($parts['port'])
            ? (string) $parts['port']
            : ($scheme === 'https' ? '443' : '80');
        $calleeRoute = isset($parts['path']) ? $parts['path'] : '/';
        if (!empty($parts['query'])) {
            $calleeRoute .= '?' . $parts['query'];
        }
        $hostDisplay = $calleeHost !== '' ? ($calleeHost . ':' . $calleePort) : '';
        $safeRequest = self::sanitize($request);
        $respSnippet = '';
        if (is_array($response)) {
            $respSnippet = mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 500) . '……';
        }
        return [
            'trace_id'       => self::currentTraceId(),
            'call_seq'       => OutboundLogUtil::nextSeq(),
            'call_direction' => 'outbound',
            'transport'      => 'http',
            'status'         => $status,
            'caller_service' => $callerSvc,
            'caller_from'    => OutboundLogUtil::callerFrame(),
            'callee_scheme'  => $scheme,
            'callee_host'    => $calleeHost,
            'callee_port'    => $calleePort,
            'callee_route'   => $calleeRoute,
            'env'            => getenv('APP_ENV') !== false && getenv('APP_ENV') !== '' ? getenv('APP_ENV') : 'prod',
            'service_name'   => $callerSvc,
            'micro_diff'     => $endMTs - $startMTs,
            'url'            => $url,
            'queryType'      => 'http',
            'host'           => $hostDisplay,
            'sourceHostName' => gethostname(),
            'request'        => json_encode($safeRequest, JSON_UNESCAPED_UNICODE),
            'response'       => $respSnippet,
            'error_message'  => (string) $errorMessage,
            'create_time'    => date('Y-m-d H:i:s'),
        ];
    }

    private static function currentTraceId(): string
    {
        if (class_exists(\xjryanse\phplite\service\WorkerRequest::class)) {
            $wr = \xjryanse\phplite\service\WorkerRequest::current();
            if ($wr !== null && $wr->traceId() !== '') {
                return $wr->traceId();
            }
        }
        return isset($GLOBALS['trace_id']) ? (string) $GLOBALS['trace_id'] : '';
    }
}
