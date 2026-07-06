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

    /**
     * SHOW CREATE TABLE（只读）
     * @param string $tableName
     * @return array<string,mixed>
     */
    public function dbCreateTableSql($tableName){
        $param['table_name'] = $tableName;
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;

        $baseUrl = 'data/db/createTableSql';
        $res = $this->queryLog($baseUrl, $param, 'curl');
        return $res['data'];
    }

    /**
     * 判断当前 dbId 连接下表是否存在
     * @return array{table_name:string,exists:bool}
     */
    public function dbTableExist($tableName){
        $param['table_name'] = $tableName;
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;

        $baseUrl = 'data/db/tableExist';
        $res = $this->queryLog($baseUrl, $param, 'curl');
        return $res['data'];
    }

    /**
     * 在当前 dbId 连接下执行 CREATE TABLE IF NOT EXISTS
     * @param string $tableName
     * @param string $createSql SHOW CREATE TABLE 得到的原始 DDL（服务端会自动改写为 IF NOT EXISTS）
     * @return array{table_name:string,created:bool,already_exists:bool}
     */
    public function dbCreateTableIfNotExists($tableName, $createSql){
        $param['table_name'] = $tableName;
        $param['create_sql'] = $createSql;
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;

        $baseUrl = 'data/db/createTableIfNotExists';
        $res = $this->queryLog($baseUrl, $param, 'curl');
        return $res['data'];
    }

}
