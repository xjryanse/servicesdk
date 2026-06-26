<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
use xjryanse\servicesdk\msgq\QLogSdk;
/**
 * 
 */
trait TableTraits{
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
    
}
