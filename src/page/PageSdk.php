<?php
namespace xjryanse\servicesdk\page;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;

/**
 * 
 */
class PageSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_page';
    
    public function pageVue($pageKey){
        // $url = static::sdkUrl('page/page/pageVue');
        $data['pageKey']  = $pageKey;
        $data['svBindId'] = $this->uuid;
        // $res              = QLogSdk::postAndLog($url, $data);
        $baseUrl        = 'page/page/pageVue';
        $res            = $this->queryLog($baseUrl, $data, 'curl');
        $data           = $res['data'];
        return $data;
    }

    /**
     * 2026年5月25日
     * @param type $pageKey
     * @return type
     */
    public function pageItemVue($pageItemId){
        $key = __CLASS__.__METHOD__.$pageItemId;
        return PCache::funcGet($key,function () use ($pageItemId) {
            $data['pageItemId'] = $pageItemId;
            $data['svBindId']   = $this->uuid;

            $baseUrl = 'page/page/itemVue';
            $res = $this->queryLog($baseUrl, $data, 'worker');
            $data             = $res['data'];
            return $data;            
        });
    }
    


}
