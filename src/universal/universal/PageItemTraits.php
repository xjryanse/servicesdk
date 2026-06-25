<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
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
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageItemId . $itemKey;
        return PCache::funcGet($key, function () use ($pageItemId, $itemKey) {
            $data = $this->postBaseData();
            $data['pageItemId'] = $pageItemId;
            $data['itemKey']    = $itemKey;

            $baseUrl = 'universal/pageItem/subList';
            $res = $this->queryLog($baseUrl, $data, 'worker');

            return $res['data'];
        });
    }
}
