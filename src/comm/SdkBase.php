<?php
namespace xjryanse\servicesdk\comm;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;
/**
 * 17点20分
 * 以DbId作为uuid:
 * DbId为 w_db_cnn表的id值；
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
abstract class SdkBase {
    use \xjryanse\phplite\traits\InstMultiTrait;
    use \xjryanse\servicesdk\comm\traits\BindSdkTrait;
    // 重写加判断
    public function __construct($uuid = 0) {
        if(!$uuid){
            throw new Exception('需要透传bindId信息，不可为空或者0');
        }
        $this->uuid = $uuid;
    }
    /**
     * 2026年1月27日：post请求的基础数据
     */
    protected function postBaseData(){
        $data['svBindId']   = $this->uuid;
        return $data;
    }
    
    /**
     * 
     * @param type $baseUrl
     * @param type $data
     * @param type $channel
     */
    public function queryLog($baseUrl, $data, $channel){
        if(!in_array($channel,['curl','worker'])){
            throw new Exception('不支持的请求方式');
        }
        if($channel == 'curl'){
            $url = static::sdkUrl($baseUrl);
            return QLogSdk::postAndLog($url, $data);
        }
        if($channel == 'worker'){
            $host       = $this->workerIp();
            $port       = $this->workerPort();
            return WQLogSdk::request($host, $port, $baseUrl, $data); 
        }
    }
}
