<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;

/**
 * 企业微信接入sdk
 */
class WeworkSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_wechat';    
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function webhookSendByMsgTplId($info){
        $baseUrl    = 'wework/webhook/sendByMsgTplId';

        $data = $this->postBaseData();
        $data['info']   = $info;
        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];        
    }
    
}
