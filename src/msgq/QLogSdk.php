<?php
namespace xjryanse\servicesdk\msgq;

use speedy\curl\Query;
use speedy\facade\Request;
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
        $res                    = Query::posturl($url, $request);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        // 调用记录日志
        QLogSdk::log($url, $request, $res);
        return $res;
    }
    
    /**
     * 记录日志
     */
    public static function log($url, $request, $response){
        $msgId = microtime(true) * 1000;
        return QLogSdk::generate($msgId, $url, $request, $response);
    }
    /**
     * 
     * 用法示例
     * 
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function generate($msgId, $url, $request, $response){
        $logUrl = 'http://'.static::sdkIp().':9907/msgq/q_log_msg/produce';        
        // 默认发本地消息中间件
        $data['msgId']          = $msgId;
        // $data['type']           = $type;
        $data['msg']            = [
            'host'      => Request::host(),
            'url'       => $url,
            'request'   => json_encode($request,JSON_UNESCAPED_UNICODE),
            'response'  => json_encode($response,JSON_UNESCAPED_UNICODE),
        ];

        $res                    = Query::posturl($logUrl, $data);

        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;

        return $resp;
    }

}
