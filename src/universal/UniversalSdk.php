<?php
namespace xjryanse\servicesdk\universal;

use xjryanse\phplite\facade\Cache;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 
 */
class UniversalSdk {
    use \xjryanse\phplite\traits\InstMultiTrait;

    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9918';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function tableDynArrs($pageItemId){
        $key = __CLASS__.__METHOD__.$pageItemId;
        return Cache::funcGet($key,function () use ($pageItemId) {
            $url = static::sdkUrl('universal/table/dynArrs');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['page_item_id'] = $pageItemId;
            $res                    = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public static function pageConfig($pageKey){
        $key = __CLASS__.__METHOD__.$pageKey;
        return Cache::funcGet($key,function () use ($pageKey) {
            $url = static::sdkUrl('universal/page/config');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['pageKey']    = $pageKey;
            $res                = QLogSdk::postAndLog($url, $data);
            return $res['data'];
        });
    }
    
}
