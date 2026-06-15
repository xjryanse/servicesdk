<?php
namespace xjryanse\servicesdk\data;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\DbSdk;

/**
 * 17点20分
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
class DataSdk extends SdkBase{

    // 2026年1月22日用数据库id，作为实例id
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_data';
    
    use \xjryanse\servicesdk\data\data\DbTraits;
    use \xjryanse\servicesdk\data\data\SqlTraits;
    use \xjryanse\servicesdk\data\data\TableTraits;
    use \xjryanse\servicesdk\data\data\TableBatchTraits;
    use \xjryanse\servicesdk\data\data\UniversalTraits;
    use \xjryanse\servicesdk\data\data\DynTraits;
    
    protected $dbId;
    public function dbBind($dbId){
        $this->dbId = $dbId;
        return $this;
    }
    /**
     * 2026年6月2日：默认业务库
     * @param type $dbCate
     */
    public function dbBindByCate($dbCate = 'dbBusi'){
        global $svBindId;
        $this->dbId = DbSdk::dbId($dbCate, $svBindId);
        return $this;
    }

    /**
     * 调试使用
     */
    public function getDbId(){
        return $this->dbId;
    }
}
