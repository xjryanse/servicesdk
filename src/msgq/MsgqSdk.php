<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\phplite\curl\Query;
use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 20251229
 */
class MsgqSdk {
    use \xjryanse\phplite\traits\InstMultiTrait;

    /**
     * 
     * @return type
     */
    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }

    /**
     * 
     * 用法示例
     * 
    public function generateTest(){
        $msgId  = '5808561031178813440';
        $type   = 'wechatWePubTplMsgSend';
        $param  = '5808561031178813440';
        
        $res    = MsgqLogic::commGenerate($msgId, $type, $param);
        return $this->dataReturn('数据操作', $res);
    }
     * 
     * 
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function commGenerate($msgId, $type, $param){
        $url = 'http://'.static::sdkIp().':9907/msgq/busi_msg/produce';        
        // 默认发本地消息中间件
        // TODO:配置解耦
        // $url = 'http://127.0.0.1:9907/msgq/busi_msg/produce';
        $data['msgId']          = $msgId;
        $data['type']           = $type;
        $data['msg']            = $param;

        $res                    = Query::posturl($url, $data);

        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;

        return $resp;
    }
    
    /**
     * 消息回调上报
     */
    public static function msgqCallBack($msgId){
        $url            = 'http://'.static::sdkIp().':9907/msgq/busi_msg/callback';
        $data['msgId']  = $msgId;
        $res            = Query::posturl($url, $data);
        return $res;
    }
    
    /**
     * 执行日志回调上报
     */
    public static function msgqLogCallBack($msgId){
        $url            = 'http://'.static::sdkIp().':9907/msgq/log_msg/callback';
        $data['msgId']  = $msgId;
        
        $res            = Query::posturl($url, $data);
        
        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;
        
        return $resp;
    }
    
}
