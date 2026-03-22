<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\servicesdk\msgq\WQLogSdk;
use Exception;

/**
 * 数据类
 */
trait DbTraits{
    /**
     * 20251227
     * @param type $sqlKey
     * @param type $orderBy
     * @param type $param
     * @return type
     * @throws Exception
     */
    public function dbTableArr(){
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;

        $baseUrl = 'data/db/tableArr';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $param);
        return $res['data'];
    }

}
