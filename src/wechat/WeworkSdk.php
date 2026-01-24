<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;

/**
 * 企业微信接入sdk
 */
class WeworkSdk {
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_wechat';

    use \xjryanse\phplite\traits\InstMultiTrait;
    use \xjryanse\servicesdk\comm\traits\BindSdkTrait;
    
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function webhookSendByMsgTplId($info){
        $baseUrl    = 'wework/webhook/sendByMsgTplId';
        $host       = static::workerIp();
        $port       = static::workerPort();

        $data['info']   = $info;
        $res        = WQLogSdk::request($host, $port, $baseUrl, $data);
        if(!$res){
            throw new Exception('消息发送不成功');
        }

        return $res['data'];        
/*
        $url = static::sdkUrl('wework/webhook/sendByMsgTplId');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['info']   = $info;
        $res            = QLogSdk::postAndLog($url, $data);
        return $res['data'];
*/
    }
    
}
