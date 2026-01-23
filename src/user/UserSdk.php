<?php
namespace xjryanse\servicesdk\user;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\servicesdk\DbSdk;
use Exception;
/**
 * 公众号接入sdk
 */
class UserSdk {
    use \xjryanse\phplite\traits\InstMultiTrait;

    protected static $serverKey = 'service_user';
    /**
     * 
     */
    protected function serverList(){
        $bindId     = $this->uuid;
        $serverKey  = static::$serverKey;
        $list = EntrySdk::serverList($bindId, $serverKey);
        dump($list);
    }

    /**
     * 
     * @return type
     */
    protected function sdkIp(){
        $serverList = $this->serverList();
        dump($serverList);
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9920';
    }

    protected function sdkUrl($path){
        return 'http://'.$this->sdkIp().':'.static::sdkPort().'/'.$path;  
    }

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
