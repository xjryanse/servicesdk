<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\phplite\tcp\Sync as TcpSync;
use xjryanse\phplite\facade\Request;
use xjryanse\phplite\curl\Query;
use xjryanse\phplite\logic\Redis;
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
        
        $startMTs   = intval(microtime(true) * 1000);
        $resp       = TcpSync::request($host, $port, $qParam);
        $endMTs     = intval(microtime(true) * 1000);
        
        //2026年1月22日
        if($resp['code'] <> 0){
            throw new Exception('接口异常:'.$host.$port.$url.'内容:'.$resp['message']);
        }

        static::log($port.':'.$url, $param, $resp, $startMTs, $endMTs);
        
        return $resp;
    }
    
    /**
     * 记录日志
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){
        $msg            = [
            'host'              => '',
            'url'               => $url,
            'start_microtime'   => $startMTs,
            'end_microtime'     => $endMTs,
            'micro_diff'        => $endMTs - $startMTs,
            'request'           => json_encode($request,JSON_UNESCAPED_UNICODE),
            'response'          => mb_substr(json_encode($response,JSON_UNESCAPED_UNICODE), 0, 500).'……',
            'create_time'       => date('Y-m-d H:i:s'),
        ];
        
        $expireKey = 'SERVICE_QUERY_LOG:'. microtime(true);
        return Redis::inst()->msgUpdate($expireKey, $msg);        
        // return static::generate($url, $request, $response, $startMTs, $endMTs);
    }
    
}
