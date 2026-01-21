<?php
namespace xjryanse\servicesdk\user;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
use Exception;
/**
 * 公众号接入sdk
 */
class UserSdk {

    protected static $globalDbId = '';
    /**
     * 2026年1月21日
     */
    public static function setGlobalDbId($dbId){
        static::$globalDbId = $dbId;
    }


    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9920';
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
    public static function login($username, $password){
        $url = static::sdkUrl('user/session/login');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['username']       = $username;
        $data['password']       = $password;
        $data['dbId']           = static::$globalDbId;
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
    public static function batchGet($userIds){
        $url        = static::sdkUrl('user/user/batchGet');
        // 默认发本地消息中间件
        $data['id']     = $userIds;
        $data['dbId']   = static::$globalDbId;
        $res            = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
}
