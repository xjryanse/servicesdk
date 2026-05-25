<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use Exception;

/**
 * pageItemVue 数据源（对标 tenancy Universal::pageItemVue 所需能力）
 * 由 service_page 编排，不在此 trait 内回调 universal/page/itemVue，避免与 PageVueTraits::pageItemVue 环回。
 */
trait PageItemVueTraits {

    /**
     * w_universal_page 表是否存在（对标 DbOperate::isTableExist('w_universal_page')）
     *
     * @return bool SDK 未对接或请求失败时返回 false，走「无本地表」分支
     */
    public function isUniversalPageTableExist(string $pageDbSource = 'dbSys'): bool {
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageDbSource;
        return PCache::funcGet($key, function () use ($pageDbSource) {
            try {
                $data = $this->postBaseData();
                $data['pageDbSource'] = $pageDbSource;
                $res = $this->queryLog('universal/page/isUniversalTableExist', $data, 'curl');
                return !empty($res['data']['exists']);
            } catch (Exception $e) {
                return false;
            }
        });
    }

    /**
     * 无本地 universal 表时回落（对标 PageSdk::pageItemVue，由 universal 服务提供数据）
     *
     * @return array|null 含 template；未对接返回 null
     */
    public function pageItemVueWhenNoLocalTable(string $pageItemId, string $pageDbSource = 'dbSys') {
        // TODO: 对接 service_universal 专用接口（勿调用 PageVueTraits::pageItemVue 防环回）
        return null;
    }

    /**
     * 页面项主记录（对标 UniversalPageItemService::getInstance($id)->get()）
     *
     * @return array|null
     */
    public function getPageItemInfo(string $pageItemId, string $pageDbSource = 'dbSys') {
        if ($pageItemId === '') {
            return null;
        }
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageItemId . $pageDbSource;
        return PCache::funcGet($key, function () use ($pageItemId, $pageDbSource) {
            $data = $this->postBaseData();
            $data['pageItemId'] = $pageItemId;
            $data['id'] = $pageItemId;
            $data['pageDbSource'] = $pageDbSource;
            $paths = [
                'universal/pageItem/info',
                'universal/page/itemInfo',
            ];
            $lastError = '';
            foreach ($paths as $path) {
                try {
                    $res = $this->queryLog($path, $data, 'curl');
                    $row = $res['data'] ?? null;
                    if (is_array($row) && $row) {
                        return $row;
                    }
                    $lastError = $path . ' 返回空数据';
                } catch (Exception $e) {
                    $lastError = $e->getMessage();
                }
            }
            throw new Exception($lastError ?: 'getPageItemInfo 失败');
        });
    }

    /**
     * 远端 base 系统回落（对标 BaseSystem::baseSysGet('/webapi/Universal/pageItemVue')）
     *
     * @return array|null
     */
    public function fetchPageItemVueRemote(string $pageItemId, array $param = []) {
        // TODO: universal/page/itemVue/remote 或 base 系统代理
        return null;
    }

    /**
     * 页面项 dynFr 模板字符串（对标 UniversalPageItemService::dynFrPageStr()）
     *
     * @return string|null
     */
    public function pageItemDynFrPageTemplate(string $pageItemId, string $source = 'admin', string $pageDbSource = 'dbSys') {
        // TODO: universal/page/item/dynFrPageStr
        return null;
    }
}
