<?php
namespace xjryanse\servicesdk\entry;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\phplite\logic\Arrays;
use xjryanse\phplite\session\RedisSession;
use Exception;
/**
 * 2025年12月30日；11点20分
 * 【静态调用】
 */
class EntrySdk {

    /**
     * todo:198专用
     * @return type
     */
    protected static function sdkIp(){
        return '127.0.0.1';
    }
    
    protected static function sdkPort(){
        return '9919';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }
    
    /**
     * 取单条数据（一般是phpfpm调用）
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function hostBindInfo($host){
        if($host == '127.0.0.1'){
            throw new Exception('不支持的域名'.$host);
        }
        $url = static::sdkUrl('entry/host/bindInfo');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['host']   = $host;

        $res                    = QLogSdk::postAndLog($url, $data);
        RedisSession::current()->set(HOST_BIND_ID, $res['id']);

        return $res['data'];
    }
    /**
     * 必传，一般是入口服务透传
     * @return type
     * @throws Exception
     */
    public static function bindIdInfo($bindId){
        $url = static::sdkUrl('entry/host/bindIdInfo');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['bindId']   = $bindId;
        $res              = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function companyKeyInfo($key){
        $url    = static::sdkUrl('entry/company/keyInfo');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['key']   = $key;

        $res    = QLogSdk::postAndLog($url, $data);
        return isset($res['data']) ? $res['data'] : null;
    }
    /**
     * 中台key,提取server列表
     * @param type $serverKey:比如db_data
     */
    public static function serverList($bindId, $serverKey):array{
        $info = static::bindIdInfo($bindId);
        $servers = Arrays::value($info, 'servers')?:[];
        return Arrays::value($servers, $serverKey) ?:[];
    }
    
}
