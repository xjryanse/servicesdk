<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;

/**
 * 缓存类
 */
trait UniversalTraits{

    /**
     * 2026年1月19日
     * @param type $finalSql
     * @return type
     * @throws Exception
     */
    public function universalFormDynSearch(){
        $baseUrl    = 'data/universal/formDynSearch';
        $param      = $this->postBaseData();        
        $param['dbId']       = $this->dbId;        

        $res        = $this->queryLog($baseUrl, $param, 'curl');
        return $res['data'];

/*
        // 默认发本地消息中间件
        // TODO:配置解耦
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $param);        
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$baseUrl);
        }
        return $res['data'];
*/

    }

}
