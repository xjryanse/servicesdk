<?php
namespace xjryanse\servicesdk\comm;

use Exception;
use Throwable;
use xjryanse\phplite\tcp\Sync as TcpSync;

/**
 * phplite TcpSync 同步调用的重试封装（连接失败、收发异常时重试）。
 *
 * 环境变量（可选）：
 * - SERVICE_TCP_MAX_ATTEMPTS：最大尝试次数，默认 3，范围 1～10
 * - SERVICE_TCP_RETRY_DELAY_MS：两次尝试间隔毫秒数，默认 100，范围 0～10000
 */
class TcpRetry {

    public static function maxAttempts(): int {
        $v = getenv('SERVICE_TCP_MAX_ATTEMPTS');
        if($v !== false && $v !== ''){
            $n = (int)$v;
            if($n >= 1){
                return min($n, 10);
            }
        }
        return 3;
    }

    public static function delayMsBetweenAttempts(): int {
        $v = getenv('SERVICE_TCP_RETRY_DELAY_MS');
        if($v !== false && $v !== ''){
            $n = (int)$v;
            if($n >= 0){
                return min($n, 10000);
            }
        }
        return 100;
    }

    /**
     * 连接预探测超时时间（毫秒），0=关闭预探测。
     */
    public static function connectProbeTimeoutMs(): int {
        $v = getenv('SERVICE_TCP_CONNECT_PROBE_TIMEOUT_MS');
        if($v !== false && $v !== ''){
            $n = (int)$v;
            if($n >= 0){
                return min($n, 10000);
            }
        }
        return 0;
    }

    /**
     * 总超时预算（毫秒），0=不限制。
     */
    public static function totalTimeoutMs(): int {
        $v = getenv('SERVICE_TCP_TOTAL_TIMEOUT_MS');
        if($v !== false && $v !== ''){
            $n = (int)$v;
            if($n >= 0){
                return min($n, 600000);
            }
        }
        return 0;
    }

    /**
     * 单次请求超时上限（秒），0=不限制（仍以调用方 $timeout 为准）。
     */
    public static function perAttemptTimeoutCapSec(): int {
        $v = getenv('SERVICE_TCP_PER_ATTEMPT_TIMEOUT_SEC');
        if($v !== false && $v !== ''){
            $n = (int)$v;
            if($n >= 0){
                return min($n, 120);
            }
        }
        return 0;
    }

    /**
     * 与 TcpSync::request 参数、返回值一致；底层异常时按配置重试后仍抛出最后一次异常。
     *
     * @param mixed $send_data
     * @return mixed
     * @throws Throwable
     */
    public static function syncRequest($host, $port, $send_data, $timeout = 20) {
        $attempts = static::maxAttempts();
        $delayMs = static::delayMsBetweenAttempts();
        $probeMs = static::connectProbeTimeoutMs();
        $totalMs = static::totalTimeoutMs();
        $startTs = microtime(true);
        $last = null;

        for($i = 0; $i < $attempts; $i++){
            if($totalMs > 0){
                $elapsedMs = (int)((microtime(true) - $startTs) * 1000);
                if($elapsedMs >= $totalMs){
                    $msg = 'TCP请求超出总耗时预算'
                        . ';host=' . $host
                        . ';port=' . $port
                        . ';attempt=' . ($i + 1)
                        . ';elapsedMs=' . $elapsedMs
                        . ';budgetMs=' . $totalMs;
                    if($last){
                        $msg .= ';last=' . $last->getMessage();
                    }
                    throw new Exception($msg);
                }
            }

            try{
                // 先做快速连通性探测，避免请求在不可达节点上长时间阻塞
                if($probeMs > 0){
                    static::probeConnectivity($host, $port, $probeMs);
                }
                $attemptTimeout = static::computeAttemptTimeout($timeout, $startTs, $totalMs);
                return TcpSync::request($host, $port, $send_data, $attemptTimeout);
            } catch(Throwable $e){
                $last = $e;
                if($i + 1 >= $attempts){
                    break;
                }
                if($delayMs > 0){
                    usleep($delayMs * 1000);
                }
            }
        }
        throw $last;
    }

    /**
     * 在发起正式请求前做一次快速 TCP 探测。
     */
    protected static function probeConnectivity($host, $port, int $probeTimeoutMs): void {
        if($probeTimeoutMs <= 0){
            return;
        }
        $timeoutSec = $probeTimeoutMs / 1000;
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeoutSec);
        if(!$fp){
            $msg = 'TCP连通性探测失败'
                . ';host=' . $host
                . ';port=' . $port
                . ';timeoutMs=' . $probeTimeoutMs
                . ';errno=' . (string)$errno
                . ';err=' . (string)$errstr;
            throw new Exception($msg);
        }
        @fclose($fp);
    }

    /**
     * 计算单次实际超时：受调用方 $timeout、总预算剩余、单次上限共同约束。
     */
    protected static function computeAttemptTimeout(int $timeout, float $startTs, int $totalMs): int {
        $effective = $timeout > 0 ? $timeout : 20;

        $cap = static::perAttemptTimeoutCapSec();
        if($cap > 0){
            $effective = min($effective, $cap);
        }

        if($totalMs > 0){
            $elapsedMs = (int)((microtime(true) - $startTs) * 1000);
            $remainMs = max(1, $totalMs - $elapsedMs);
            $remainSec = max(1, (int)ceil($remainMs / 1000));
            $effective = min($effective, $remainSec);
        }

        return max(1, $effective);
    }
}
