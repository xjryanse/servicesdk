<?php
namespace xjryanse\servicesdk\data;

use xjryanse\servicesdk\comm\SdkBase;
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

}
