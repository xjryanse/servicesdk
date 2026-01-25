<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\tcp\Sync as TcpSync;
use xjryanse\phplite\logic\Redis;
use xjryanse\phplite\logic\Arrays;
use Exception;
/**
 * 2026年1月14日：使用workerman调用请求
 * 20251227:20点15分
 */
class WQLogSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_msgq';

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

        $urlStr = $host.':'.$port.'/'.$url;
        
        //2026年1月22日
        if(!$resp || $resp['code'] <> 0){
            $msgStr = Arrays::value($resp, 'message');
            throw new Exception(gethostname().'接口异常:'.$urlStr.'内容:'.$msgStr.'请求参数'. json_encode($param, JSON_UNESCAPED_UNICODE));
        }

        static::log($urlStr, $param, $resp, $startMTs, $endMTs);
        
        return $resp;
    }
    
    /**
     * 记录日志
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){
        // 记录服务间的链路调用关系
        global $serviceTraceArr;        
        $msg            = [
            'url'               => $url,
            'micro_diff'        => $endMTs - $startMTs,
            'queryType'         => 'workerman',
            'host'              => '',
            // 请求源主机标识
            'sourceHostName'    => gethostname(),
            'request'           => json_encode($request,JSON_UNESCAPED_UNICODE),
            'response'          => mb_substr(json_encode($response,JSON_UNESCAPED_UNICODE), 0, 500).'……',
            'create_time'       => date('Y-m-d H:i:s'),
        ];
        
        $tMsg = $msg;
        $tMsg['serviceTrace']   = $serviceTraceArr;
        $serviceTraceArr[]      = $tMsg;

        // 存储链路间调用关系
        $expireKey = 'SERVICE_QUERY_LOG:'. microtime(true);
        return Redis::inst()->msgUpdate($expireKey, $msg);        
        // return static::generate($url, $request, $response, $startMTs, $endMTs);
    }
    
}
