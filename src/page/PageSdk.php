<?php
namespace xjryanse\servicesdk\page;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\cache\PCache;

/**
 * service_page 调用 SDK（缓存统一在本层 PCache）
 */
class PageSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_page';

    /** pageVue 结果缓存 TTL（秒），0=关闭；可用 SDK_PAGE_VUE_TTL 覆盖 */
    protected static function pageVueSdkTtl(): int {
        $v = getenv('SDK_PAGE_VUE_TTL');
        if ($v !== false && $v !== '') {
            return (int) $v;
        }

        return 60;
    }

    /**
     * @param string $pageKey
     * @param array<string, mixed> $context source/pageDbSource/vueVersion/fresh 等渲染维度
     */
    public function pageVue($pageKey, array $context = []){
        $pageDbSource = (string) ($context['pageDbSource'] ?? 'dbSys');
        $source = (string) ($context['source'] ?? 'admin');
        $vueVersion = (string) ($context['vueVersion'] ?? '2');
        $fresh = !empty($context['fresh']);

        $data = [
            'pageKey'      => $pageKey,
            'svBindId'     => $this->uuid,
            'pageDbSource' => $pageDbSource,
            'source'       => $source,
            'vueVersion'   => $vueVersion,
        ];

        if ($fresh || static::pageVueSdkTtl() <= 0) {
            return $this->fetchPageVue($data);
        }

        $sessionUserId = $this->sessionUserId();
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageDbSource . $source . $vueVersion . $pageKey. $sessionUserId;

        return PCache::funcGet($key, function () use ($data) {
            return $this->fetchPageVue($data);
        }, static::pageVueSdkTtl());
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function fetchPageVue(array $data) {
        $baseUrl = 'page/page/pageVue';
        $res = $this->queryLog($baseUrl, $data, 'worker');

        return $res['data'];
    }

    /**
     * @param string $pageItemId
     * @param array<string, mixed> $context fresh 等
     */
    public function pageItemVue($pageItemId, array $context = []){
        $source = (string) ($context['source'] ?? 'admin');
        $vueVersion = (string) ($context['vueVersion'] ?? '2');
        $fresh = !empty($context['fresh']);

        $data = [
            'pageItemId' => $pageItemId,
            'svBindId'   => $this->uuid,
            'source'       => $source,
            'vueVersion'   => $vueVersion,
        ];

        if ($fresh || static::pageVueSdkTtl() <= 0) {
            return $this->fetchPageItemVue($data);
        }

        $sessionUserId = $this->sessionUserId();
        $key = __CLASS__ . __METHOD__ . $this->uuid . $source . $vueVersion . $pageItemId. $sessionUserId;

        return PCache::funcGet($key, function () use ($data) {
            return $this->fetchPageItemVue($data);
        }, static::pageVueSdkTtl());
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function fetchPageItemVue(array $data) {
        $baseUrl = 'page/page/itemVue';
        $res = $this->queryLog($baseUrl, $data, 'worker');

        return $res['data'];
    }
}
