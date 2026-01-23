<?php
namespace xjryanse\servicesdk\data;

use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 17点20分
 * 以DbId作为uuid:
 * DbId为 w_db_cnn表的id值；
 * 
 */
class DataSdk {

    // 2026年1月22日用数据库id，作为实例id
    use \xjryanse\phplite\traits\InstMultiTrait;
    
    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9914';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }

    use \xjryanse\servicesdk\data\data\SqlTraits;
    use \xjryanse\servicesdk\data\data\TableTraits;
    use \xjryanse\servicesdk\data\data\TableBatchTraits;
    
    
    /**
     * 
     */
    protected static function workerIp(){
        return '127.0.0.1';
    }

    protected static function workerPort(){
        return '19914';
    }

}
