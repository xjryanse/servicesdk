<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 公众号接入sdk
 */
class WepubSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_wechat';

    /**
     * 公众号模板消息 relay
     */
    public function wePubTplRelay($info){
        $baseUrl = 'wepub/relay/wePubTpl';

        $data = $this->postBaseData();
        $data['info'] = $info;
        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }

    /**
     * 公众号模板消息投递前预检（校验 openid 等，不产生发送记录）
     */
    public function wePubTplPrecheck($info){
        $baseUrl = 'wepub/relay/wePubTplPrecheck';

        $data = $this->postBaseData();
        $data['info'] = $info;
        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }
    /**
     * 2026年6月13日
     * @param type $openid
     * @return type
     */
    public function wePubFansBindUserIds($openid){
        $baseUrl        = 'wepub/fans/bindUserIds';

        $data           = $this->postBaseData();
        $data['openid'] = $openid;
        $res            = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }

}
