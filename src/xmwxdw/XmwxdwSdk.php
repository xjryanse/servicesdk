<?php
namespace xjryanse\servicesdk\xmwxdw;

use xjryanse\servicesdk\comm\SdkBase;
/**
 * 公众号接入sdk
 */
class XmwxdwSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_xmwxdw';

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function planTimeBusInfo($carNo, $time){
        $baseUrl = 'xmwxdw/plan/timeBusInfo';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['car_no']     = $carNo;
        $data['time']       = $time;
        $data['svBindId']   = $this->uuid;
        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }

}
