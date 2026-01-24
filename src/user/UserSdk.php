<?php
namespace xjryanse\servicesdk\user;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
use Exception;
/**
 * 公众号接入sdk
 */
class UserSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_user';

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function login($username, $password){
        $url = static::sdkUrl('user/session/login');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['username']       = $username;
        $data['password']       = $password;
        $data['svBindId']       = $this->uuid;
        $res                    = QLogSdk::postAndLog($url, $data);
        if($res['code'] <>0){
            throw new Exception($res['message']);
        }
        return $res['data'];
    }
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function batchGet($userIds){
        $url        = static::sdkUrl('user/user/batchGet');
        // 默认发本地消息中间件
        $data['id']         = $userIds;
        $data['svBindId']   = $this->uuid;
        $res                = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
}
