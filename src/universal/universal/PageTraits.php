<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
/**
 * 
 */
trait PageTraits{
    
    /**
     * 整页 dynenum 配置（PreData / rawBack 等单条数据场景）。
     */
    public function pageDynArrs($pageId){
        $key = __CLASS__.__METHOD__.$pageId;
        return PCache::funcGet($key,function () use ($pageId) {
            $data = $this->postBaseData();
            $data['page_id'] = $pageId;
            $data['pageId'] = $pageId;
            $res = $this->queryLog('universal/page/dynArrs', $data, 'curl');
            return $res['data'];
        });
    }
    
    /**
     * page 单条 catalog 原始行（base_table / page_key 等）
     */
    public function pageGet($pageId)
    {
        $key = __CLASS__ . __METHOD__ . $pageId;
        return PCache::funcGet($key, function () use ($pageId) {
            $data = $this->postBaseData();
            $data['id'] = $pageId;
            $res = $this->queryLog('universal/page/get', $data, 'curl');

            return $res['data'];
        });
    }
    
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageConfig($pageKey, $pageDbSource){
        $sessionUserId = $this->sessionUserId();
        $key = __CLASS__.__METHOD__.$pageKey.$this->uuid.$sessionUserId;
        // PCache::rm($key);        
        return PCache::funcGet($key,function () use ($pageKey, $pageDbSource) {
            // $url = static::sdkUrl('universal/page/config');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data = $this->postBaseData();            
            $data['pageKey']        = $pageKey;
            $data['pageDbSource']   = $pageDbSource;

            $baseUrl    = 'universal/page/config';
            $res        = $this->queryLog($baseUrl, $data, 'worker');
            // $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function defaultPageKey($cate){
        $key = __CLASS__.__METHOD__.$cate;
        // PCache::rm($key);        
        return PCache::funcGet($key,function () use ($cate) {
            $baseUrl = 'universal/page/defaultPageKey';
            // 默认发本地消息中间件
            $data               = $this->postBaseData();            
            $data['cate']       = $cate;

            $res                = $this->queryLog($baseUrl, $data, 'worker');
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
        // PCache::rm($key);
        return PCache::funcGet($key,function () {
            $baseUrl    = 'universal/page/keyObj';
            // 默认发本地消息中间件
            $data       = $this->postBaseData();            
            $res        = $this->queryLog($baseUrl, $data, 'worker');
            return $res['data'];
        });
    }
}
