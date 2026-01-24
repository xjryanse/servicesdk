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

        $res                = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
    /**
     * 
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

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }


}
