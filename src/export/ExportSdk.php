<?php
namespace xjryanse\servicesdk\export;

use xjryanse\servicesdk\msgq\WQLogSdk;
use xjryanse\speedy\curl\Query;
/**
 * 车载定位微服务接入sdk
 * 9904
 */
class ExportSdk {
    /**
     * 
     */
    protected static function workerIp(){
        return '127.0.0.1';
    }

    protected static function workerPort(){
        return '19904';
    }
    /**
     * 同步导出excel
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function excelSync($exportData, $dataTitle){
        $url = 'http://10.9.0.202:9924/export/excel/sync';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['exportData']  = $exportData;
        $data['dataTitle']   = $dataTitle;
        
// dump(json_encode($data,JSON_UNESCAPED_UNICODE));exit;
// dump(json_encode($dataTitle,JSON_UNESCAPED_UNICODE));
        $res                 = Query::posturl($url, $data);

        return $res['data'];
    }
    

}
