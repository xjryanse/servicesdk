<?php
namespace xjryanse\servicesdk\sql\sql;

use xjryanse\phplite\cache\SqlCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 缓存类
 */
trait SqlTableTraits{
    /**
     * 2026年5月9日
     * @param string $sqlId
     * @param array $param
     * @return type
     */
    public function sqlTableForGenerate(string $sqlId, array $param = []){
        $pMd5   = Arrays::md5($param);
        $key    = __CLASS__.__METHOD__.$sqlId.$pMd5;
        // SqlCache::rm($key);
        $arr = SqlCache::funcGet($key,function () use ($sqlId, $param) {
            $baseUrl        = 'sql/sqlTable/forGenerate';
            $data           = $this->postBaseData();
            $data['sqlId']  = $sqlId;
            $data['param']  = $param;
            $res = $this->queryLog($baseUrl, $data, 'worker');
            return $res['data'];            
        });
        if(!$arr){
            SqlCache::rm($key);
        }

        return $arr;
    }
}
