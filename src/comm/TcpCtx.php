<?php

namespace xjryanse\servicesdk\comm;

/**
 * Workerman TCP 包调用上下文：{ url, param, ctx }。
 */
class TcpCtx
{
    /** @var list<string> */
    private static $ctxKeys = [
        'trace_id', 'caller_service', 'caller_route', 'caller_from',
        'caller_ip', 'caller_runtime', 'caller_peer_ip',
    ];

    /**
     * @param array<string,mixed> $bizParam
     * @return array<string,mixed>
     */
    public static function envelope(string $url, array $bizParam = []): array
    {
        return [
            'url'   => $url,
            'param' => $bizParam,
            'ctx'   => self::buildForOutbound(),
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function buildForOutbound(): array
    {
        $wr = self::workerRequest();
        $traceId = self::resolveTraceId($wr);

        if ($wr !== null && $wr->ctx() !== []) {
            $ctx = $wr->ctx();
            $ctx['trace_id'] = $traceId;
            return self::normalize($ctx);
        }

        return self::normalize([
            'trace_id'       => $traceId,
            'caller_service' => OutboundLogUtil::callerServiceLabel(),
            'caller_route'   => self::currentRoute(),
            'caller_from'    => OutboundLogUtil::callerFrame(),
            'caller_ip'      => self::localIp(),
            'caller_runtime' => self::currentRuntime(),
        ]);
    }

    /**
     * 从 TCP 请求包 ctx 字段解析入站调用方信息（不写 global）。
     *
     * @param array<string,mixed> $reqArr
     * @return array<string,string>
     */
    public static function absorbFromRequest(array $reqArr, string $peerIp = ''): array
    {
        $ctx = [];
        if (!empty($reqArr['ctx']) && is_array($reqArr['ctx'])) {
            $ctx = self::normalize($reqArr['ctx']);
        }

        if ($peerIp !== '' && empty($ctx['caller_peer_ip'])) {
            $ctx['caller_peer_ip'] = $peerIp;
        }

        return $ctx;
    }

    /**
     * @param array<string,string> $ctx
     * @param array<string,mixed> $base
     * @return array<string,mixed>
     */
    public static function mergeIntoErrNoticeCtx(array $ctx, array $base): array
    {
        foreach (self::$ctxKeys as $key) {
            $value = trim((string) ($ctx[$key] ?? ''));
            if ($value !== '') {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    /**
     * @return object|null AppRequest 实例（phplite 未加载时为 null）
     */
    private static function workerRequest()
    {
        if (!class_exists(\xjryanse\phplite\service\AppRequest::class)) {
            return null;
        }
        return \xjryanse\phplite\service\AppRequest::current();
    }

    /**
     * @param object|null $wr
     */
    private static function resolveTraceId($wr): string
    {
        if ($wr !== null && method_exists($wr, 'traceId')) {
            $traceId = trim((string) $wr->traceId());
            if ($traceId !== '') {
                return $traceId;
            }
        }
        $traceId = trim((string) ($GLOBALS['trace_id'] ?? ''));
        if ($traceId === '') {
            $traceId = uniqid('t' . substr((string) microtime(true), -6) . '_', true);
            if ($wr === null) {
                $GLOBALS['trace_id'] = $traceId;
            }
        }
        return $traceId;
    }

    private static function currentRoute(): string
    {
        $wr = self::workerRequest();
        if ($wr !== null) {
            return $wr->url();
        }
        return '';
    }

    private static function currentRuntime(): string
    {
        $wr = self::workerRequest();
        if ($wr !== null) {
            return $wr->runtime();
        }
        return php_sapi_name() === 'cli' ? 'worker' : 'phpfpm';
    }

    private static function localIp(): string
    {
        if (class_exists(\xjryanse\phplite\logic\Network::class)) {
            foreach (\xjryanse\phplite\logic\Network::serverIps() as $ip) {
                $ip = trim((string) $ip);
                if ($ip !== '' && strpos($ip, '10.') === 0) {
                    return $ip;
                }
            }
        }
        if (!empty($_SERVER['SERVER_ADDR'])) {
            return (string) $_SERVER['SERVER_ADDR'];
        }
        $ip = @gethostbyname(gethostname());
        if ($ip && $ip !== gethostname()) {
            return $ip;
        }
        return 'unknown';
    }

    /**
     * @param array<string,mixed> $ctx
     * @return array<string,string>
     */
    private static function normalize(array $ctx): array
    {
        $out = [];
        foreach (self::$ctxKeys as $key) {
            if (isset($ctx[$key]) && trim((string) $ctx[$key]) !== '') {
                $out[$key] = trim((string) $ctx[$key]);
            }
        }
        return $out;
    }
}
