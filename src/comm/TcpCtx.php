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
     * 标准 Workerman TCP 请求包。
     *
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
        $traceId = trim((string) ($GLOBALS['trace_id'] ?? ''));
        if ($traceId === '') {
            $traceId = uniqid('t' . substr((string) microtime(true), -6) . '_', true);
            $GLOBALS['trace_id'] = $traceId;
        }

        if (!empty($GLOBALS['inbound_caller_ctx']) && is_array($GLOBALS['inbound_caller_ctx'])) {
            $ctx = $GLOBALS['inbound_caller_ctx'];
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
     * 从 TCP 请求包 ctx 字段解析入站调用方信息。
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
        if (!empty($ctx['trace_id'])) {
            $GLOBALS['trace_id'] = $ctx['trace_id'];
        }
        if ($ctx !== []) {
            $GLOBALS['inbound_caller_ctx'] = $ctx;
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

    private static function currentRoute(): string
    {
        if (!empty($GLOBALS['err_notice_ctx']['url'])) {
            return trim((string) $GLOBALS['err_notice_ctx']['url']);
        }
        return '';
    }

    private static function currentRuntime(): string
    {
        $runtime = trim((string) ($GLOBALS['err_notice_ctx']['runtime'] ?? ''));
        if ($runtime !== '') {
            return $runtime;
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
