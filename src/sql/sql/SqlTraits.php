<?php
namespace xjryanse\servicesdk\sql\sql;

use xjryanse\phplite\cache\SqlCache;
use xjryanse\phplite\logic\Arrays;
/**
 * 缓存类
 */
trait SqlTraits{
    
    /**
     * 2026年5月9日
     * @param string $sqlKey
     * @param array $param
     * @return type
     */
    public function keyToId(string $sqlKey,array $param = []){
        $pMd5 = Arrays::md5($param);
        $key = __CLASS__.__METHOD__.$sqlKey.$pMd5;
        // SqlCache::rm($key);
        $sqlId = SqlCache::funcGet($key,function () use ($sqlKey, $param) {
            $baseUrl = 'sql/sql/keyToId';
            $data           = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $data['param']  = $param;
            $res = $this->queryLog($baseUrl, $data, 'worker');
            return $res['data'];            
        });
        return $sqlId;
    }
    /**
     * 优化成功：20260115
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public function keyBaseSql(string $sqlKey, array $param = []){
        $pMd5 = Arrays::md5($param);
        $key = __CLASS__.__METHOD__.$sqlKey.$pMd5;
        $sql = SqlCache::funcGet($key, function () use ($sqlKey, $param) {
            $baseUrl = 'sql/sql/keyBaseSql';
            $data = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $data['param']  = $param;
            $res = $this->queryLog($baseUrl, $data, 'worker');
            return $res['data'];
        });
        return $sql;
    }


    /**
     * 执行校验
     * @param type $sqlKey
     * @param type $param
     */
    public function searchFields($sqlKey){
        $key = __CLASS__.__METHOD__.$sqlKey;
        $res = SqlCache::funcGet($key,function () use ($sqlKey) {        
            $baseUrl = 'sql/sql/searchFields';
            $data           = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $res = $this->queryLog($baseUrl, $data, 'worker');
            return $res['data'];                
        });
        return $res;
    }

    public function sqlGet($sqlId)
    {
        $key = __CLASS__ . __METHOD__ . $sqlId;
        return SqlCache::funcGet($key, function () use ($sqlId) {
            $data = $this->postBaseData();
            $data['id'] = $sqlId;
            $res = $this->queryLog('sql/sql/get', $data, 'worker');

            return $res['data'];
        });
    }
    /**
     * sqlKey
     * @param type $sqlKey
     * @return type
     */
    public function sqlGetByKey($sqlKey){
        $key = __CLASS__ . __METHOD__ . $sqlKey;
        return SqlCache::funcGet($key, function () use ($sqlKey) {
            $data = $this->postBaseData();
            $data['sqlKey'] = $sqlKey;
            $res = $this->queryLog('sql/sql/getByKey', $data, 'worker');
            return $res['data'];
        });
    }

    public function sumFields($sqlId, array $param = [])
    {
        $pMd5 = Arrays::md5($param);
        $key = __CLASS__ . __METHOD__ . $sqlId . $pMd5;
        return SqlCache::funcGet($key, function () use ($sqlId, $param) {
            $data = $this->postBaseData();
            $data['sqlId'] = $sqlId;
            $data['param'] = $param;
            $res = $this->queryLog('sql/sql/sumFields', $data, 'worker');

            return $res['data'];
        });
    }

    public function fieldAsByType($sqlId, $fieldType)
    {
        $key = __CLASS__ . __METHOD__ . $sqlId . $fieldType;
        return SqlCache::funcGet($key, function () use ($sqlId, $fieldType) {
            $data = $this->postBaseData();
            $data['sqlId'] = $sqlId;
            $data['fieldType'] = $fieldType;
            $res = $this->queryLog('sql/sql/fieldAsByType', $data, 'worker');

            return $res['data'];
        });
    }
}
