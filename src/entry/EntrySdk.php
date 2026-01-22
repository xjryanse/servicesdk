<?php
namespace xjryanse\servicesdk\entry;

use xjryanse\phplite\facade\Request;
use xjryanse\servicesdk\msgq\QLogSdk;
use Exception;
/**
 * 2025年12月30日；11点20分
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
     * 取单挑数据
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
     * 20251227:服务类ip
     * @return type
     */
    public static function serveIp(){
        // $host = Request::host();
        $host = $_SERVER['SERVER_NAME'];
        // 2026年1月22日：固化
        $host = Request::host() ?: md5(ROOT_PATH);
        $info = static::hostBindInfo($host);
        if(!$info['msgq_ip']){
            throw new Exception('域名'.$host .'未配置msgq_ip参数');
        }
        return $info['msgq_ip'];
    }
    
}
