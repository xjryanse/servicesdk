<?php
namespace xjryanse\servicesdk\comm;

use xjryanse\servicesdk\comm\TcpRetry;
/**
 * 异常消息通知1
 */
class Worker {

    /**
     * 2026年2月4日
     * @param type $host
     * @param type $port
     * @param type $baseUrl
     * @param type $param
     * @return type
     */
    public static function query($host, $port, $baseUrl , $param ){
        $qParam['url']   = $baseUrl;
        $qParam['param'] = $param;
        
        return TcpRetry::syncRequest($host, $port, $qParam);
    }
    
}
