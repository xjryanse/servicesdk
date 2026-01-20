<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\phplite\logic\Url;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;

/**
 * 缓存类
 */
trait SqlTraits{
    /**
     * 20251227
     * @param type $sqlKey
     * @param type $orderBy
     * @param type $param
     * @return type
     * @throws Exception
     */
    public static function sqlDataPaginate($sqlKey, $orderBy='', $param=[]){
        $url = static::sdkUrl('data/sql/paginate');
        
        $param['sqlKey']        = $sqlKey;
        if($orderBy){
            $param['orderBy']    = $orderBy;
        }

        $baseUrl = 'data/sql/paginate';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $host = static::workerIp();
        $port = static::workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $param);        
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }

    /**
     * 20260102
     * @param type $sqlParam
     * @return type
     * @throws Exception
     */
    public static function rawSqlQuery($sqlParam){
        $url = static::sdkUrl('data/sql/rawSqlQuery');
        
        $param                   = [];
        $param['sqlParam']         = $sqlParam;

        $res                    = QLogSdk::postAndLog($url, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }
    /**
     * 2026年1月19日
     * @param type $finalSql
     * @param array $bind       绑定参数： ['id' => 10, 'name' => '张三']
     * @return type
     * @throws Exception
     */
    public static function sqlQuery($finalSql,array $bind = []){
        $param['sql']           = $finalSql;
        $param['bind']          = $bind;
        $baseUrl = 'data/sql/query';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $host = static::workerIp();
        $port = static::workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $param);        
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$baseUrl);
        }
        return $res['data'];
    }

}
