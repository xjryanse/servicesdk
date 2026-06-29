<?php
namespace xjryanse\servicesdk\comm;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\phplite\logic\Arrays;
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
        $this->querySvInst();
    }
    /**
     * 用svBindId作为实例
     */
    public static function svInst(){
        global $svBindId;
        return static::inst($svBindId);
    }

    /**
     * 请求后向接口时使用
     */
    public function querySvInst(){
        $service = $this->serverInfoRand();
        $serverSvBindId = Arrays::value($service, 'server_sv_bind_id');
        return $serverSvBindId ?: $this->uuid;
    }

    /**
     * 2026年5月10日
     * 带当前域名：传统phpfpm使用
     */
    public static function currentHostSvInst(){
        $svBindId = EntrySdk::currentHostBindId();
        return static::inst($svBindId);
    }
    
    /**
     * 2026年1月27日：post请求的基础数据
     * 2026年6月29日根据server_sv_bind_id获取实例
     * 如果server_sv_bind_id不为空，则使用server_sv_bind_id作为实例
     * 否则使用uuid作为实例
     * 
     * @return array<string,mixed>
     */
    protected function postBaseData(){
        $data['svBindId']   = $this->querySvInst();
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
    
    /**
     * 集中管理缓存规则
     * @param string $method
     * @param type $subFix
     * @return string
     */
    protected function generateCacheKey(string $method, $subFix = null): string {
        $key = static::class.$method . md5($this->sdkIp());
        if ($subFix !== null) {
            $key .= $subFix;
        }
        return $key;
    }
}
