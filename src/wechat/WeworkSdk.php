<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\servicesdk\msgq\QLogSdk;


/**
 * 企业微信接入sdk
 */
class WeworkSdk {

    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9908';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }

    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function webhookSendByMsgTplId($info){
        $url = static::sdkUrl('wework/webhook/sendByMsgTplId');

        dump($url);
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['info']   = $info;
        dump($data);
        $res            = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
}
