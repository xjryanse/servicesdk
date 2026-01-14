<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\speedy\tcp\Sync as TcpSync;
/**
 * 2026年1月14日：使用workerman调用请求
 * 20251227:20点15分
 */
class WQLogSdk {
    /**
     * 
     */
    public static function request($host, $port, $url, $param){
        $qParam = [];
        $qParam['url']   = $url;
        $qParam['param'] = $param;
        
        
        $startTs    = round(microtime(true) * 1000);
        $resp       = TcpSync::request($host, $port, $qParam);
        $endTs      = round(microtime(true) * 1000);
        // 请求耗时
        $resp['tTs'] = $endTs- $startTs;
        
        return $resp;
    }
    
    

}
