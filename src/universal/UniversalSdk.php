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
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['page_item_id'] = $pageItemId;
            $res                    = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }

}
