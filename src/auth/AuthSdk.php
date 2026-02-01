<?php
namespace xjryanse\servicesdk\auth;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\msgq\QLogSdk;
use Exception;
/**
 * 公众号接入sdk
 */
class AuthSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_auth';

    
    /**
     * 后台菜单
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function adminMenu($userId){
        $baseUrl = 'auth/admin/menu';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data = $this->postBaseData();

        $res = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }
}
