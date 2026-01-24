<?php
namespace xjryanse\servicesdk\gps;

use xjryanse\servicesdk\msgq\WQLogSdk;
/**
 * 车载定位微服务接入sdk
 * 9904
 */
class GpsSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_gps';

    /**
     * 供应商设备列表
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function supplierEquipmentMapList(){
        $baseUrl    = 'gps/supplier/equipmentMapList';
        
        $postP      = [];

        $host       = $this->workerIp();
        $port       = $this->workerPort();
        $res        = WQLogSdk::request($host, $port, $baseUrl, $postP);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }
    

}
