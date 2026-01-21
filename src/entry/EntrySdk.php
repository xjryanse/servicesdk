<?php
namespace xjryanse\servicesdk\entry;

use xjryanse\phplite\curl\Query;
use xjryanse\phplite\logic\Arrays;

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
        $url = static::sdkUrl('entry/host/bindInfo');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['host']   = $host;

        $res                    = Query::posturl($url, $data);
        return $res['data'];
    }

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function companyKeyInfo($key){
        $url = static::sdkUrl('entry/company/keyInfo');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['key']   = $key;

        $res                    = Query::posturl($url, $data);
        return $res['data'];
    }
    
    /**
     * 20251227:服务类ip
     * @return type
     */
    public static function serveIp(){
        // $host = Request::host();
        // 20260121:先host:host, 没有则server_name
        $serverPort = Arrays::value($_SERVER, 'SERVER_PORT') ? : 0;
        if($serverPort >= 9900){
            //【1】9900以上端口部署微服务：一个微服务只连接一个库
            $host = md5(ROOT_PATH);
        } else {
            //【2】普通端口，部署站点
            $host = $_SERVER['HTTP_HOST'];
        }
        
        $info = static::hostBindInfo($host);
        return $info && $info['msgq_ip'] ? $info['msgq_ip'] : '127.0.0.1';
    }
    
}
