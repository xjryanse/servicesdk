<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\cache\PCache;
/**
 * 
 */
trait FormTraits{
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
}
