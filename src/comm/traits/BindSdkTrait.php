<?php
namespace xjryanse\servicesdk\comm\traits;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\phplite\logic\Arrays;
use Exception;
/**
 * 缓存类
 * 适用于以bindId透传的微服务
 */
trait BindSdkTrait{
    
    protected $serverInfo;
    /**
     * 外部传入，目前在service_entry入口中使用
     */
    public function serverInfoSet($serverInfo){
        $this->serverInfo = $serverInfo;
    }
    
    /**
     * 
     */
    protected function serverInfoRand(){
        if(!$this->serverInfo){
            $bindId     = $this->uuid;
            if(!is_numeric($bindId)){
                throw new Exception('不支持的绑定号'.$bindId.'请使用serverInfoSet方法透传参数');
            }
            $serverKey  = static::$serverKey;
            $list       = EntrySdk::serverList($bindId, $serverKey);
            if(!$list){
                $info = EntrySdk::bindIdInfo($bindId);
                $host = Arrays::value($info,'host');
                throw new Exception('绑定号'.$bindId.'对应的域名'.$host.'没有配置'.$serverKey.'服务的参数，请联系运维配置');
            }
            // 随机从多个数组中取一条记录
            $this->serverInfo = $list[array_rand($list)];
        }
        return $this->serverInfo;
    }

    /**
     *  ["id"] => string(19) "5826535652000903169"
        ["host"] => string(15) "admin.axslwl.cn"
        ["server_key"] => string(12) "service_user"
        ["workerman_ip"] => string(10) "10.9.0.198"
        ["workerman_port"] => string(5) "19920"
        ["http_ip"] => string(10) "10.9.0.198"
        ["http_port"] => string(4) "9
     * @return type
     */
    protected function sdkIp(){
        $serverInfo = $this->serverInfoRand();
        return $serverInfo['http_ip'];
    }
    
    protected function sdkPort(){
        $serverInfo = $this->serverInfoRand();
        return $serverInfo['http_port'];
    }

    protected function sdkUrl($path){
        return 'http://'.$this->sdkIp().':'.static::sdkPort().'/'.$path;  
    }

    protected function workerIp(){
        $serverInfo = $this->serverInfoRand();
        return $serverInfo['workerman_ip'];
    }

    protected function workerPort(){
        $serverInfo = $this->serverInfoRand();
        return $serverInfo['workerman_port'];
    }
    

}
