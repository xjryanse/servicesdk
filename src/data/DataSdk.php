<?php
namespace xjryanse\servicesdk\data;

use xjryanse\servicesdk\comm\SdkBase;
use Exception;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;

/**
 * 17点20分
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
class DataSdk extends SdkBase{

    // 2026年1月22日用数据库id，作为实例id
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_data';
    
    use \xjryanse\servicesdk\data\data\SqlTraits;
    use \xjryanse\servicesdk\data\data\TableTraits;
    use \xjryanse\servicesdk\data\data\TableBatchTraits;
    
    protected $dbId;
    public function dbBind($dbId){
        $this->dbId = $dbId;
        return $this;
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
