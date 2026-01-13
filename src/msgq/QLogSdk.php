<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\speedy\curl\Query;
use xjryanse\speedy\facade\Request;
use Exception;
/**
 * 请求日志调用sdk
 * 20251227:20点15分
 */
class QLogSdk {

    protected static function sdkIp(){
        return '127.0.0.1';
    }

    /**
     * 接口post请求:并记录日志
     */
    public static function postAndLog($url, $request){
        // 脚本请求开始
        $startMTs   = intval(microtime(true) * 1000);
        
        $res                    = Query::posturl($url, $request);
        
dump($url);
dump(json_encode($request,JSON_UNESCAPED_UNICODE));
dump($res);

        // 脚本请求结束
        $endMTs     = intval(microtime(true) * 1000);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        // 调用记录日志
        static::log($url, $request, $res, $startMTs, $endMTs);
        return $res;
    }
    
    /**
     * 记录日志
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){
        $msgId = microtime(true) * 1000;
        return static::generate($msgId, $url, $request, $response, $startMTs, $endMTs);
    }
    /**
     * 
     * 用法示例
     * 
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function generate($msgId, $url, $request, $response, $startMTs, $endMTs){
        $logUrl = 'http://'.static::sdkIp().':9907/msgq/q_log_msg/produce';        
        // 默认发本地消息中间件
        $data['msgId']          = $msgId;
        // $data['type']           = $type;
        $data['msg']            = [
            'host'              => Request::host(),
            'url'               => $url,
            'start_microtime'   => $startMTs,
            'end_microtime'     => $endMTs,
            'micro_diff'        => $endMTs - $startMTs,
            'request'           => json_encode($request,JSON_UNESCAPED_UNICODE),
            'response'          => json_encode($response,JSON_UNESCAPED_UNICODE),
        ];

        $res                    = Query::posturl($logUrl, $data);

        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;

        return $resp;
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
