<?php

namespace xjryanse\servicesdk\entry\db;

use Exception;
use xjryanse\phplite\cache\SCache;

trait DbTraits
{
    public static function dbInfo($dbId)
    {
        $cacheKey = static::generateCacheKey('dbInfo', $dbId);
        return SCache::funcGet($cacheKey, function () use ($dbId) {
            $res = static::wQuery('entry/dbCnn/get', ['id' => $dbId]);
            return static::parseEntryData($res, 'dbCnn/get');
        });
    }

    /**
     * @throws Exception
     */
    public static function bindByDbId(string $dbId): string
    {
        $dbId = trim($dbId);
        if ($dbId === '' || $dbId === '0') {
            throw new Exception('dbId 不能为空');
        }

        $cacheKey = static::generateCacheKey('bindByDbId', $dbId);
        $resp = SCache::funcGet($cacheKey, function () use ($dbId) {
            $res = static::wQuery('entry/dbCnn/bindByDbId', ['dbId' => $dbId]);
            $payload = static::parseEntryData($res, 'dbCnn/bindByDbId');
            $bindId = trim((string) ($payload['bindId'] ?? ''));
            if ($bindId === '') {
                throw new Exception('entry dbCnn/bindByDbId 未返回 bindId');
            }
            return $bindId;
        });

        return is_string($resp) ? $resp : '';
    }
}
