<?php
namespace xjryanse\servicesdk\comm;

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
     * 与 TcpSync::request 参数、返回值一致；底层异常时按配置重试后仍抛出最后一次异常。
     *
     * @param mixed $send_data
     * @return mixed
     * @throws Throwable
     */
    public static function syncRequest($host, $port, $send_data, $timeout = 20) {
        $attempts = static::maxAttempts();
        $delayMs = static::delayMsBetweenAttempts();
        $last = null;
        for($i = 0; $i < $attempts; $i++){
            try{
                return TcpSync::request($host, $port, $send_data, $timeout);
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
}
