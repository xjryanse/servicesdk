<?php

namespace xjryanse\servicesdk\comm;

/**
 * 出站调用日志辅助：同请求内序号、调用方帧，避免 WQLogSdk/QLogSdk 重复实现。
 */
class OutboundLogUtil {

    /**
     * 同一次 HTTP/CLI 请求内单调递增；跨请求用 REQUEST_TIME_FLOAT+pid 区分重置。
     */
    public static function nextSeq(): int {
        $rid = (string) ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true)) . '@' . getmypid();
        if (($GLOBALS['__outbound_log_rid'] ?? '') !== $rid) {
            $GLOBALS['__outbound_log_rid'] = $rid;
            $GLOBALS['__outbound_log_seq'] = 0;
        }
        return ++$GLOBALS['__outbound_log_seq'];
    }

    /**
     * 当前进程作为调用方展示名（写入 service_name / caller_service）。
     * 优先项目根目录名（如 service_zzcr），与 LogBuffer 落盘目录一致；可覆盖。
     */
    public static function callerServiceLabel(): string {
        $v = getenv('LOG_PROJECT_SLUG');
        if ($v !== false && $v !== '') {
            return preg_replace('/[^a-zA-Z0-9._-]/', '_', $v);
        }
        if (defined('ROOT_PATH') && ROOT_PATH !== '') {
            $base = basename(rtrim(ROOT_PATH, '/\\'));
            if ($base !== '') {
                return $base;
            }
        }
        $v = getenv('SERVICE_NAME');
        if ($v !== false && $v !== '') {
            return $v;
        }
        return gethostname() ?: 'unknown';
    }

    /**
     * 跳过 SDK 内部帧，取第一条业务调用栈（谁发起了这次出站请求）。
     */
    public static function callerFrame(): string {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
        foreach ($bt as $frame) {
            $class = $frame['class'] ?? '';
            $fn = $frame['function'] ?? '';
            if ($class === '') {
                continue;
            }
            if (strpos($class, 'xjryanse\\servicesdk\\msgq\\WQLogSdk') === 0) {
                continue;
            }
            if (strpos($class, 'xjryanse\\servicesdk\\msgq\\QLogSdk') === 0) {
                continue;
            }
            if (strpos($class, 'xjryanse\\servicesdk\\comm\\TcpRetry') === 0) {
                continue;
            }
            if (strpos($class, 'xjryanse\\phplite\\tcp\\Sync') === 0) {
                continue;
            }
            if (strpos($class, 'xjryanse\\servicesdk\\comm\\OutboundLogUtil') === 0) {
                continue;
            }
            return $class . '::' . $fn;
        }
        return '';
    }
}
