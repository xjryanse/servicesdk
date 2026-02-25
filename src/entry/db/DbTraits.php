<?php

namespace xjryanse\servicesdk\entry\db;

use xjryanse\phplite\cache\SCache;
/**
 * 缓存类
 */
trait DbTraits {
    /**
     * 取单条数据（一般是phpfpm调用）
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function dbInfo($dbId){
        $cacheKey = static::generateCacheKey(__FUNCTION__, $dbId);
        $resp = SCache::funcGet($cacheKey, function () use ($dbId){        
            $baseUrl      = 'entry/dbCnn/get';
            $data['id']   = $dbId;
            $res = static::wQuery($baseUrl, $data);
            return $res['data'];
        });
        if(!$resp){
            SCache::rm($cacheKey);
        }
        return $resp;
    }
}

