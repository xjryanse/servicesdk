<?php
namespace xjryanse\servicesdk\universal;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;
/**
 * 
 */
class UniversalSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_universal';

    use \xjryanse\servicesdk\universal\universal\PageTraits;
    use \xjryanse\servicesdk\universal\universal\PageVueTraits;
    use \xjryanse\servicesdk\universal\universal\PageItemTraits;

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function tableDynArrs($pageItemId){
        $key = __CLASS__.__METHOD__.$pageItemId;
        return PCache::funcGet($key,function () use ($pageItemId) {
            $url = static::sdkUrl('universal/table/dynArrs');
            // 2026年2月1日
            $data = $this->postBaseData();
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['page_item_id'] = $pageItemId;
            $res                    = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }

    /**
     * 2026年6月：表单字段单条配置（uniDynSearch 等）
     */
    public function formGet($fieldId){
        $key = __CLASS__.__METHOD__.$fieldId;
        return PCache::funcGet($key,function () use ($fieldId) {
            $data = $this->postBaseData();
            $data['id'] = $fieldId;
            $res = $this->queryLog('universal/form/get', $data, 'curl');
            return $res['data'];
        });
    }

    /**
     * 通用动态枚举搜索。
     */
    public function dynSearch(array $param){
        $data = array_merge($this->postBaseData(), $param);
        $res = $this->queryLog('universal/dynSearch/search', $data, 'curl');
        return $res['data'];
    }

    /**
     * page_item 单条 catalog 原始行（field_filter / auth_check 等）
     */
    public function pageItemGet($pageItemId)
    {
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
    public function pageItemInfo($pageItemId)
    {
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
     * 表格列 optionArr（对标 UniversalItemTableService::optionArr）
     */
    public function tableOptionArr($pageItemId)
    {
        $key = __CLASS__ . __METHOD__ . $this->uuid . $pageItemId;
        return PCache::funcGet($key, function () use ($pageItemId) {
            $data = $this->postBaseData();
            $data['page_item_id'] = $pageItemId;
            $data['pageItemId'] = $pageItemId;
            $res = $this->queryLog('universal/pageItem/tableDynArrs', $data, 'curl');

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

}
