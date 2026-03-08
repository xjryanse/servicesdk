<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;
/**
 * 
 */
trait PageVueTraits{
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageVue($pageKey){
        $key = __CLASS__.__METHOD__.$pageKey;
        PCache::rm($key);
        return PCache::funcGet($key,function () use ($pageKey) {
            $url = static::sdkUrl('universal/page/vue');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['pageKey']    = $pageKey;
            $data['svBindId']   = $this->uuid;
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageItemVue($pageKey, $pageDbSource){
        $key = __CLASS__.__METHOD__.$pageKey;
        PCache::rm($key);
        return PCache::funcGet($key,function () use ($pageKey, $pageDbSource) {
            $url = static::sdkUrl('universal/page/itemVue');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data = $this->postBaseData();
            $data['pageKey']        = $pageKey;
            $data['pageDbSource']   = $pageDbSource;
            
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }

}
