<?php
namespace xjryanse\servicesdk\msgq;


use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\curl\Query;
use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 20251229
 */
class MsgqSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_msgq';

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
    public function commGenerate($msgId, $type, $param){
        $url = 'http://'.static::sdkIp().':9907/msgq/busi_msg/produce';        
        // 默认发本地消息中间件
        // TODO:配置解耦
        // $url = 'http://127.0.0.1:9907/msgq/busi_msg/produce';
        $data['msgId']          = $msgId;
        $data['type']           = $type;
        $data['msg']            = $param;
        //2026年1月26日
        $data['svBindId']       = $this->uuid;

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
    public function msgqCallBack($msgId){
        $url            = 'http://'.$this->sdkIp().':9907/msgq/busi_msg/callback';
        $data['msgId']  = $msgId;
        $res            = Query::posturl($url, $data);
        return $res;
    }
    
    /**
     * 执行日志回调上报
     */
    public function msgqLogCallBack($msgId){
        $url            = 'http://'.$this->sdkIp().':9907/msgq/log_msg/callback';
        $data['msgId']  = $msgId;
        
        $res            = Query::posturl($url, $data);
        
        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;
        
        return $resp;
    }
    
}
