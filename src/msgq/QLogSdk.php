<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\curl\Query;
use xjryanse\phplite\facade\Request;
use xjryanse\phplite\logic\Redis;
use Exception;
/**
 * 请求日志调用sdk
 * 20251227:20点15分
 */
class QLogSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_msgq';

    /**
     * 接口post请求:并记录日志
     */
    public static function postAndLog($url, $request){
        // 脚本请求开始
        $startMTs   = intval(microtime(true) * 1000);
        
        $res                    = Query::posturl($url, $request);

        // 脚本请求结束
        $endMTs     = intval(microtime(true) * 1000);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url.'参数:'. json_encode($request,JSON_UNESCAPED_UNICODE));
        }
        //2026年1月22日
        if($res['code']<>0){
            throw new Exception('接口异常:'.$url.'内容:'.$res['message'].'请求参数:'.json_encode($request,JSON_UNESCAPED_UNICODE));
        }
        
        // 调用记录日志
        static::log($url, $request, $res, $startMTs, $endMTs);
        return $res;
    }
    
    /**
     * 记录日志
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){

        $msg            = [
            'host'              => Request::host(),
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

    }
    
    /**
     * 执行日志回调上报
     */
    public static function callBack($msgId){
        $url            = 'http://'.static::sdkIp().':9907/msgq/q_log_msg/callback';
        $data['msgId']  = $msgId;
        
        $res            = Query::posturl($url, $data);
        
        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;
        
        return $resp;
    }
}
