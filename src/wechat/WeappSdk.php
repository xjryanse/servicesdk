<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 微信小程序接入sdk
 */
class WeappSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_wechat';
   /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function getComKeyByAppId($appid){
        $baseUrl        = 'weapp/company/getComKey';
        $data           = $this->postBaseData();
        $data['appid']  = $appid;
        $res            = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];        
    }
    /**
     * 2026年2月2日
     * @param type $appid
     * @return type
     */
    public function getByAppid($appid){
        $baseUrl        = 'weapp/weapp/getByAppid';
        $data           = $this->postBaseData();
        $data['appid']  = $appid;
        $res            = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];        
    }
    
}
