<?php
namespace xjryanse\servicesdk;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\phplite\logic\Arrays;
use Exception;
/**
 * 异常消息通知1
 */
class DbSdk {

    /**
     * 
     * @param type $dbCate
     * @return type
     */
    public static function dbId($dbCate, $hostBindId = ''){
        if($dbCate == 'dbEntry'){
            return 0;
        }
        if($hostBindId){
            // 透传id直接取
            $confArr    = EntrySdk::bindIdInfo($hostBindId);
        } else {
            // 没有的用域名取
            $httpHost   = $_SERVER['HTTP_HOST'];
            $confArr    = EntrySdk::hostBindInfo($httpHost);
        }
        // 业务库配置
        $dbInfo     = Arrays::value($confArr, $dbCate);
        return $dbInfo['id'];
    }
    
}
