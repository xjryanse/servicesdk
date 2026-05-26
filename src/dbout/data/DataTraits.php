<?php

namespace xjryanse\servicesdk\dbout\data;

use xjryanse\phplite\curl\Query;
use Exception;

/**
 * service_dbout 导出接口
 */
trait DataTraits
{
    /**
     * 
     * @param type $tableName
     * @param type $createTime
     * @param type $channel
     * @return type
     */
    public function dataCreates($dbId, $tableName, $createTime = '', $lastId = '', $channel = 'curl')
    {
        $baseUrl    = 'dbout/data/creates';        

        $param['table_name']  = $tableName;
        $param['create_time'] = $createTime;
        $param['dbId']          = $dbId;
        if ($lastId !== '') {
            $param['lastId'] = $lastId;
        }
        
        $data   = $this->postBaseData();
        $qParam = array_merge($param, $data);
        $res    = $this->queryLog($baseUrl, $qParam, $channel);
        return $res['data'];        
    }
    /**
     * 
     * @param type $tableName
     * @param type $updateTime
     * @param type $channel
     * @return type
     */
    public function dataUpdates($dbId, $tableName, $updateTime = '', $lastId = '', $channel = 'curl'){
        $baseUrl    = 'dbout/data/updates';        

        $param['table_name']  = $tableName;
        $param['update_time'] = $updateTime;
        $param['dbId']          = $dbId;
        if ($lastId !== '') {
            $param['lastId'] = $lastId;
        }
        
        $data   = $this->postBaseData();
        $qParam = array_merge($param, $data);
        $res    = $this->queryLog($baseUrl, $qParam, $channel);
        return $res['data'];        
        
    }

}
