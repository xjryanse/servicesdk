<?php

namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;

/**
 * 
 */
trait PageItemTraits {

    /**
     * page_item 单条 catalog 原始行（field_filter / auth_check 等）
     */
    public function pageItemGet($pageItemId) {
        $key = __CLASS__ . __METHOD__ . $pageItemId;
        return PCache::funcGet($key, function () use ($pageItemId) {
                    $data = $this->postBaseData();
                    $data['id'] = $pageItemId;
                    $data['pageItemId'] = $pageItemId;
                    $res = $this->queryLog('universal/pageItem/get', $data, 'curl');

                    return $res['data'];
                });
    }

    /**
     * page_item 完整配置（含 optionArr，对标 UniversalPageItemService::get）
     */
    public function pageItemInfo($pageItemId) {
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageItemId;
        return PCache::funcGet($key, function () use ($pageItemId) {
                    $data = $this->postBaseData();
                    $data['id'] = $pageItemId;
                    $data['pageItemId'] = $pageItemId;
                    $res = $this->queryLog('universal/pageItem/info', $data, 'curl');

                    return $res['data'];
                });
    }

    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function pageItemSubList($pageItemId, $itemKey) {
        $key = __CLASS__ . __METHOD__ . $pageItemId . $itemKey;
        return PCache::funcGet($key, function () use ($pageItemId, $itemKey) {
                    // TODO:配置解耦
                    $data = $this->postBaseData();
                    $data['pageItemId'] = $pageItemId;
                    $data['itemKey'] = $itemKey;

                    $baseUrl = 'universal/pageItem/subList';
                    $res = $this->queryLog($baseUrl, $data, 'curl');
                    // $res                = QLogSdk::postAndLog($url, $data);
                    return $res['data'];
                });
    }
}
