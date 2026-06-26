<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
/**
 * 
 */
trait BtnTraits{
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function btnTableDynArrs($btnId){
        $key = __CLASS__.__METHOD__.$btnId;
        return PCache::funcGet($key,function () use ($btnId) {
            // 2026年2月1日
            $data = $this->postBaseData();
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['id'] = $btnId;
            $res = $this->queryLog('universal/btn/tableDynArrs', $data, 'curl');
            return $res['data'];
        });
    }
}
