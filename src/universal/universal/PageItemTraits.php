<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;
/**
 * 
 */
trait PageItemTraits{
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageItemSubList($pageItemId, $itemKey){
        $key = __CLASS__.__METHOD__.$pageItemId.$itemKey;
        return PCache::funcGet($key,function () use ($pageItemId, $itemKey) {
            // TODO:配置解耦
            $data = $this->postBaseData();            
            $data['pageItemId'] = $pageItemId;
            $data['itemKey']    = $itemKey;

            $baseUrl    = 'universal/pageItem/subList';
            $res        = $this->queryLog($baseUrl, $data, 'curl');
            // $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
}
