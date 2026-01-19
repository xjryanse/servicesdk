<?php
namespace xjryanse\servicesdk\sql;

use xjryanse\servicesdk\entry\EntrySdk;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use xjryanse\phplite\facade\Cache;
use xjryanse\phplite\logic\Arrays;
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
     * 
     */
    protected static function workerIp(){
        return '127.0.0.1';
    }

    protected static function workerPort(){
        return '19911';
    }
    
    /**
     * 优化成功：20260115
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public static function keyBaseSql(string $sqlKey,array $param = []){
        $pMd5 = Arrays::md5($param);
        $key = __CLASS__.__METHOD__.$sqlKey.$pMd5;
        // dump($key);
        return Cache::funcGet($key,function () use ($sqlKey, $param) {
            $baseUrl = 'sql/sql/keyBaseSql';
            $data           = [];
            $data['sqlKey'] = $sqlKey;
            $data['param']  = $param;
            
            $host = static::workerIp();
            $port = static::workerPort();
            $res = WQLogSdk::request($host, $port, $baseUrl, $data);
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
    
    /**
     * joinSql生成逻辑
     * @param type $sqlKey
     * @param type $param
     * 
{
  ["tables"] => array(2) {
    [0] => array(2) {
      ["table"] => string(25) "lvBaoCprTdxNoDetectNotice"
      ["alias"] => string(3) "bbA"
    }
    [1] => array(4) {
      ["table"] => string(13) "w_msg_mid_log"
      ["alias"] => string(3) "bbB"
      ["join"] => string(4) "left"
      ["on"] => string(28) "bbA.id = bbB.data_unique_key"
    }
  }
  ["where"] => array(1) {
    [0] => string(37) "bbA.startTime<= '2026-02-01 20:21:34'"
  }
}
     */
    public static function joinSqlGenerate($sqlParam){
        $keyRaw = md5(json_encode($sqlParam, JSON_UNESCAPED_UNICODE));
        $key = __CLASS__.__METHOD__.$keyRaw;
        return Cache::funcGet($key,function () use ($sqlParam) {        
            $url    = static::sdkUrl('sql/sql/joinSqlGenerate');
            $data           = [];
            $data['sqlParam'] = $sqlParam;

            $res            = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
}
