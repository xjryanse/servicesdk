<?php
namespace xjryanse\servicesdk\universal\universal;

/**
 * 
 */
trait PageVueTraits{
    /** SDK 侧 pageVue 二次缓存 TTL（秒），默认 0=关闭，避免“双层缓存”调试困难 */
    protected static function pageVueSdkTtl(): int {
        $v = getenv('SDK_PAGEVUE_TTL');
        return ($v !== false && $v !== '') ? (int)$v : 0;
    }

    /**
     * 2026年1月23日
     * @param string $pageKey
     * @return array 含 template/script/style 等，以及 _fromCache：true=缓存取，false=接口取
     */
    public function pageVue($pageKey){
        $baseUrl    = 'universal/page/vue';
        // 默认发本地消息中间件
        $data       = $this->postBaseData();  
        $data['pageKey']  = $pageKey;
        $data['svBindId'] = $this->uuid;
        $res        = $this->queryLog($baseUrl, $data, 'worker');

        return static::pageVueWithFromCache($res['data'], false);
    }

    /**
     * 为 pageVue 返回值追加 _fromCache 标记
     */
    protected static function pageVueWithFromCache($data, $fromCache) {
        $arr = is_array($data) ? $data : ['data' => $data];
        $arr['_fromCache'] = $fromCache;
        return $arr;
    }
    
    /**
     * 2026年1月23日
     * @param string $pageKey
     * @param string $pageDbSource
     * @return array 含 _fromCache：true=缓存取，false=接口取
     */
    public function pageItemVue($pageKey, $pageDbSource){
        $baseUrl    = 'universal/page/itemVue';
        // 默认发本地消息中间件
        $data       = $this->postBaseData();  
        $data['pageKey']      = $pageKey;
        $data['pageDbSource'] = $pageDbSource;        
        $res        = $this->queryLog($baseUrl, $data, 'worker');
        return static::pageVueWithFromCache($res['data'], false);
    }

}
