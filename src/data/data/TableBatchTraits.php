<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\servicesdk\msgq\QLogSdk;

/**
 * 缓存类
 */
trait TableBatchTraits{
    /**
     * 
     * @param type $tableName
     * @param type $tableData   二维数组；带id
     * @return type
     */
    public static function tableBatchDataUpdate($tableName,$tableData){
        $url = static::sdkUrl('data/tableBatch/update');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['table_data'] = $tableData;

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }


}
