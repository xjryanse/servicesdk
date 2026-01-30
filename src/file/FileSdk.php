<?php
namespace xjryanse\servicesdk\file;

use xjryanse\servicesdk\comm\SdkBase;
/**
 * 17点20分
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
class FileSdk extends SdkBase{

    // 2026年1月22日用数据库id，作为实例id
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_file';
    /**
     * 优化成功：20260115
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public function uploadFromUrl(string $url){
        $baseUrl        = 'file/upload/fromUrl';
        $data           = $this->postBaseData();
        $data['url']    = $url;
        
        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];     
    }
}
