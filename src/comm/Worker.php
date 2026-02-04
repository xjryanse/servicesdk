<?php
namespace xjryanse\servicesdk\comm;

use xjryanse\phplite\tcp\Sync as TcpSync;
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
        
        return TcpSync::request($host, $port, $qParam);
    }
    
}
