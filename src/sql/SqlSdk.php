<?php
namespace xjryanse\servicesdk\sql;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\servicesdk\msgq\QLogSdk;
use speedy\facade\Cache;
use Exception;
/**
 * 调用sql中台的极简sdk
 * 2025年12月28日21点15分
 */
class SqlSdk {

    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9911';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }
    
    /**
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public static function keyBaseSql($sqlKey){
        $key = __CLASS__.__METHOD__.$sqlKey;
        return Cache::funcGet($key,function () use ($sqlKey) {
            $url    = static::sdkUrl('sql/sql/keyBaseSql');
            $data           = [];
            $data['sqlKey'] = $sqlKey;
            $res            = QLogSdk::postAndLog($url, $data);
            if(!$res['data']){
                throw new Exception('没有获取到sql配置,请排查'.$sqlKey);
            }
            return $res['data'];            
        });
    }

    /**
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public static function searchFields($sqlKey){
        $key = __CLASS__.__METHOD__.$sqlKey;
        return Cache::funcGet($key,function () use ($sqlKey) {        
            $url    = static::sdkUrl('sql/sql/searchFields');
            $data           = [];
            $data['sqlKey'] = $sqlKey;

            $res            = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    
}
