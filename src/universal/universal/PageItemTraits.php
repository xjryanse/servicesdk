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
    public function pageItemSubList($pageItemId, $itemKey, $pageDbSource = 'dbSys'){
        $key = __CLASS__.__METHOD__.$pageItemId.$itemKey.$pageDbSource;
        return PCache::funcGet($key,function () use ($pageItemId, $itemKey, $pageDbSource) {
            // TODO:配置解耦
            $data = $this->postBaseData();            
            $data['pageItemId'] = $pageItemId;
            $data['page_item_id'] = $pageItemId;
            $data['itemKey']    = $itemKey;
            $data['pageDbSource'] = $pageDbSource;

            $baseUrl    = 'universal/pageItem/subList';
            $res        = $this->queryLog($baseUrl, $data, 'curl');
            // $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
}
