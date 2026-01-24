<?php
namespace xjryanse\servicesdk\data;

use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 17点20分
 * 以DbId作为uuid:
 * DbId为 w_db_cnn表的id值；
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
class DataSdk {

    // 2026年1月22日用数据库id，作为实例id
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_data';

    use \xjryanse\phplite\traits\InstMultiTrait;
    use \xjryanse\servicesdk\comm\traits\BindSdkTrait;
    
    use \xjryanse\servicesdk\data\data\SqlTraits;
    use \xjryanse\servicesdk\data\data\TableTraits;
    use \xjryanse\servicesdk\data\data\TableBatchTraits;
    
    protected $dbId;
    protected function dbId($dbId){
        $this->dbId = $dbId;
        return $this;
    }

}
