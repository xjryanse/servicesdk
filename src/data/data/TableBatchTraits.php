<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\servicesdk\msgq\QLogSdk;

/**
 * 缓存类
 */
trait TableBatchTraits{
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function tableBatchDataGet($tableName,$ids){
        $url = static::sdkUrl('data/tableBatch/get');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['id']         = $ids;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $res                = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
    /**
     * 【ok】
     * @param type $tableName
     * @param type $tableData   二维数组；带id
     * @return type
     */
    public function tableBatchDataUpdate($tableName,$tableData){
        $url = static::sdkUrl('data/tableBatch/update');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['table_data'] = $tableData;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    /**
     * 【ok】
     * @param type $tableName
     * @param type $ids   一维id
     * @return type
     */
    public function tableBatchDataDelete($tableName,$ids){
        $url = static::sdkUrl('data/tableBatch/delete');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name']         = $tableName;
        $data['table_data']['id']   = $ids;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    /**
     * 2026年1月25日
     * @param type $tableName
     * @param type $tableData   二维数组；带id
     * @return type
     */
    public function tableBatchDataInsert($tableName,$tableData){
        $url = static::sdkUrl('data/tableBatch/insert');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['table_data'] = $tableData;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
    /**
     * 2026年1月25日
     * @param type $tableName
     * @param type $tableData   二维数组；带id
     * @return type
     */
    public function tableBatchDataSave($tableName,$tableData, $uniqueField = 'id'){
/*
 *      不开发了。由本地先处理，再决定是新增；更新还是删除；减少io开销
 * 
        $url = static::sdkUrl('data/tableBatch/save');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name']     = $tableName;
        $data['table_data']     = $tableData;
        // 唯一字段：一般是id
        $data['unique_field']   = $uniqueField;
        // 2026年1月21日：新增dbId入参
        $data['dbId']           = $this->dbId;
        $data['svBindId']       = $this->uuid;

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
 * 
 */
    }

}
