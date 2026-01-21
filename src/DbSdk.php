<?php
namespace xjryanse\servicesdk;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\phplite\logic\Arrays;
/**
 * 异常消息通知1
 */
class DbSdk {

    /**
     * 
     * @param type $dbCate
     * @return type
     */
    public static function dbId($dbCate){
        if($dbCate == 'dbEntry'){
            return 0;
        }
        // 客户站点
        $httpHost   = $_SERVER['HTTP_HOST'];
        $confArr    = EntrySdk::hostBindInfo($httpHost);
        // 业务库配置
        $dbInfo     = Arrays::value($confArr, $dbCate);
        return $dbInfo['id'];
    }
    
}
