<?php

namespace xjryanse\servicesdk\entry\backup;

use Exception;
use xjryanse\phplite\cache\SCache;

/**
 * 备份配置：TCP entry/backup/*
 */
trait BackupTraits
{
    /**
     * @return array<string,mixed>
     * @throws Exception
     */
    public static function backupProfile(string $sourceDbId, string $targetDbId, string $role = 'dbin'): array
    {
        $sourceDbId = trim($sourceDbId);
        $targetDbId = trim($targetDbId);
        $role = strtolower(trim($role)) ?: 'dbin';
        if ($sourceDbId === '' || $targetDbId === '') {
            throw new Exception('sourceDbId 与 targetDbId 不能为空');
        }

        $suffix = $role . '_' . $sourceDbId . '_' . $targetDbId;
        $cacheKey = static::generateCacheKey('backupProfile', $suffix);
        $resp = SCache::funcGet($cacheKey, function () use ($sourceDbId, $targetDbId, $role) {
            $res = static::wQuery('entry/backup/profile', [
                'sourceDbId' => $sourceDbId,
                'targetDbId' => $targetDbId,
                'role' => $role,
            ]);
            return static::parseEntryData($res, 'backup/profile');
        });
        return is_array($resp) ? $resp : [];
    }

    /**
     * @return list<array<string,mixed>>
     * @throws Exception
     */
    public static function backupSyncPlanList(): array
    {
        $cacheKey = static::generateCacheKey('backupSyncPlanList', 'all');
        $resp = SCache::funcGet($cacheKey, function () {
            $res = static::wQuery('entry/backup/syncPlanList', []);
            $payload = static::parseEntryData($res, 'backup/syncPlanList');
            $plans = $payload['plans'] ?? [];
            return is_array($plans) ? $plans : [];
        });
        return is_array($resp) ? $resp : [];
    }
}
