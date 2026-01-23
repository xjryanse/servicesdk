<?php
namespace xjryanse\servicesdk\gps;

use xjryanse\servicesdk\msgq\WQLogSdk;
/**
 * 车载定位微服务接入sdk
 * 9904
 */
class GpsSdk {
    use \xjryanse\phplite\traits\InstMultiTrait;
    
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
     * 供应商设备列表
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function supplierEquipmentMapList(){
        $baseUrl    = 'gps/supplier/equipmentMapList';
        
        $postP      = [];

        $host       = static::workerIp();
        $port       = static::workerPort();
        $res        = WQLogSdk::request($host, $port, $baseUrl, $postP);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }
    

}
