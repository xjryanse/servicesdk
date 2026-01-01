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
    public static function tableBatchDataGet($tableName,$ids){
        $url = static::sdkUrl('data/tableBatch/get');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['id']         = $ids;
dump($url);
dump($data);



        $res                = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
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
