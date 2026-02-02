<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;
/**
 * 
 */
trait PageTraits{
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageConfig($pageKey){
        $key = __CLASS__.__METHOD__.$pageKey;
        PCache::rm($key);        
        return PCache::funcGet($key,function () use ($pageKey) {
            $url = static::sdkUrl('universal/page/config');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data = $this->postBaseData();            
            $data['pageKey']    = $pageKey;
            
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function defaultPageKey($companyId, $cate){
        $key = __CLASS__.__METHOD__.$companyId.$cate;
        // PCache::rm($key);        
        return PCache::funcGet($key,function () use ($companyId, $cate) {
            $baseUrl = 'universal/page/defaultPageKey';
            // 默认发本地消息中间件
            $data               = $this->postBaseData();            
            $data['company_id'] = $companyId;
            $data['cate']       = $cate;

            $res                = $this->queryLog($baseUrl, $data, 'curl');
            return $res['data'];
        });
    }
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
    public function pageItemVue($pageKey){
        $key = __CLASS__.__METHOD__.$pageKey;
        PCache::rm($key);
        return PCache::funcGet($key,function () use ($pageKey) {
            $url = static::sdkUrl('universal/page/itemVue');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['pageKey']    = $pageKey;
            $data['svBindId']   = $this->uuid;
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    /**
     * 2026年1月26日
     * @param type $pageKey
     * @return type
     */
    public function pageKeyObj(){
        $key = __CLASS__.__METHOD__;
        PCache::rm($key);
        return PCache::funcGet($key,function () {
            $url = static::sdkUrl('universal/page/keyObj');
            // 默认发本地消息中间件
            // TODO:配置解耦
            // $data['pageKey']    = $pageKey;
            $data['svBindId']   = $this->uuid;
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
}
