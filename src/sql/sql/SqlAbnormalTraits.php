<?php
namespace xjryanse\servicesdk\sql\sql;

use xjryanse\phplite\cache\SqlCache;
use xjryanse\phplite\logic\Arrays;
/**
 * 缓存类
 */
trait SqlAbnormalTraits{
    

    /**
     * 异常检测元数据（abnormal.db），勿传 catalog
     */
    public function keyBaseSqlAbnormal(string $sqlKey, array $param = []){
        $pMd5 = Arrays::md5($param);
        $key = __CLASS__.__METHOD__.$sqlKey.$pMd5;
        return SqlCache::funcGet($key, function () use ($sqlKey, $param) {
            $data = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $data['param']  = $param;
            $res = $this->queryLog('sql/sql/keyBaseSqlAbnormal', $data, 'worker');
            return $res['data'];
        });
    }

    public function keyToIdAbnormal(string $sqlKey, array $param = []){
        $data = $this->postBaseData();
        $data['sqlKey'] = $sqlKey;
        $data['param']  = $param;
        $res = $this->queryLog('sql/sql/keyToIdAbnormal', $data, 'worker');
        return $res['data'];
    }

    public function searchFieldsAbnormal(string $sqlKey){
        $cacheKey = __CLASS__.__METHOD__.$sqlKey;
        return SqlCache::funcGet($cacheKey, function () use ($sqlKey) {
            $data = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $res = $this->queryLog('sql/sql/searchFieldsAbnormal', $data, 'worker');
            return $res['data'];
        });
    }

    /** @return list<string> */
    public function listKeysAbnormal(){
        $cacheKey = __CLASS__ . __METHOD__;
        return SqlCache::funcGet($cacheKey, function () {
            $data = $this->postBaseData();
            $res  = $this->queryLog('sql/sql/listKeysAbnormal', $data, 'worker');
            $list = $res['data'] ?? [];
            return is_array($list) ? $list : [];
        }, 60);
    }

}
