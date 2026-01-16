<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\speedy\tcp\Sync as TcpSync;
use xjryanse\speedy\facade\Request;
use xjryanse\speedy\curl\Query;
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
        
        static::log($port.':'.$url, $param, $resp, $startMTs, $endMTs);
        
        return $resp;
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
        $logUrl = 'http://127.0.0.1:9907/msgq/q_log_msg/produce';        
        // 默认发本地消息中间件
        $data['msgId']          = $msgId;
        // $data['type']           = $type;
        $data['msg']            = [
            'host'              => '',
            'url'               => $url,
            'start_microtime'   => $startMTs,
            'end_microtime'     => $endMTs,
            'micro_diff'        => $endMTs - $startMTs,
            'request'           => json_encode($request,JSON_UNESCAPED_UNICODE),
            'response'          => mb_substr(json_encode($response,JSON_UNESCAPED_UNICODE), 0, 500).'……',
        ];

        $res                    = Query::posturl($logUrl, $data);

        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;

        return $resp;
    }
}
