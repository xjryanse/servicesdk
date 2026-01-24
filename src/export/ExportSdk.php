<?php
namespace xjryanse\servicesdk\export;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\curl\Query;
/**
 * 车载定位微服务接入sdk
 * 9904
 */
class ExportSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_export';

    /**
     * 同步导出 excel
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function excelSync ($exportData, $dataTitle) {
        $url = 'http://10.9.0.2:9924/export/excel/sync';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['exportData']  = $exportData;
        $data['dataTitle']   = $dataTitle;
        $res                 = Query::posturl($url, $data);

        return $res['data'];
    }

    /**
     * 同步导出 csv 文件
     * @param type $exportData
     * @param type $dataTitle
     * @return type
     */
    public static function csvSync ($exportData, $dataTitle) {
        $url = 'http://10.9.0.2:9924/export/csv/sync';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['exportData']  = $exportData;
        $data['dataTitle']   = $dataTitle;
        $res                 = Query::posturl($url, $data);

        return $res['data'];
    }
    
    

}
