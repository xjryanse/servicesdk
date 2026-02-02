<?php
namespace xjryanse\servicesdk\config;

use xjryanse\servicesdk\comm\SdkBase;
/**
 * 公众号接入sdk
 */
class ConfigSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_config';

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function config($module = ''){
        $baseUrl = 'config/config/config';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data = $this->postBaseData();
        $data['module']   = $module;
        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }

}
