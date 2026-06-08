<?php

namespace xjryanse\servicesdk\entry\backup;

use xjryanse\phplite\cache\SCache;
use Exception;

/**
 * 备份配置：从 service_entry 拉取 w_db_backup_profile（TCP entry/backup/profile）
 */
trait BackupTraits {

    /**
     * 按 sourceDbId + targetDbId + role 获取备份 profile 配置。
     *
     * @param string $sourceDbId 源库 w_db_cnn.id
     * @param string $targetDbId 备份库 w_db_cnn.id
     * @param string $role dbin|dbout
     * @return array<string,mixed>
     * @throws Exception
     */
    public static function backupProfile(string $sourceDbId, string $targetDbId, string $role = 'dbin'): array {
        $sourceDbId = trim($sourceDbId);
        $targetDbId = trim($targetDbId);
        $role = strtolower(trim($role)) ?: 'dbin';
        if ($sourceDbId === '' || $targetDbId === '') {
            throw new Exception('sourceDbId 与 targetDbId 不能为空');
        }

        $cacheKey = static::generateCacheKey(__FUNCTION__, $role . '_' . $sourceDbId . '_' . $targetDbId);
        $resp = SCache::funcGet($cacheKey, function () use ($sourceDbId, $targetDbId, $role) {
            $baseUrl = 'entry/backup/profile';
            $data = [
                'sourceDbId' => $sourceDbId,
                'targetDbId' => $targetDbId,
                'role' => $role,
            ];
            $res = static::wQuery($baseUrl, $data);
            if (!is_array($res) || (int) ($res['code'] ?? 1) !== 0) {
                $msg = is_array($res) && isset($res['message'])
                    ? (string) $res['message']
                    : 'entry backup/profile 失败';
                throw new Exception($msg);
            }
            $payload = $res['data'] ?? null;
            if (!is_array($payload)) {
                throw new Exception('entry backup/profile 返回 data 无效');
            }
            return $payload;
        });

        if (!is_array($resp) || $resp === []) {
            SCache::rm($cacheKey);
        }
        return is_array($resp) ? $resp : [];
    }

    /**
     * 显式同步计划（sourceDbId + targetDbId）
     *
     * @return list<array<string,mixed>>
     * @throws Exception
     */
    public static function backupSyncPlanList(): array
    {
        $cacheKey = static::generateCacheKey(__FUNCTION__, 'all');
        $resp = SCache::funcGet($cacheKey, function () {
            $res = static::wQuery('entry/backup/syncPlanList', []);
            if (!is_array($res) || (int) ($res['code'] ?? 1) !== 0) {
                $msg = is_array($res) && isset($res['message'])
                    ? (string) $res['message']
                    : 'entry backup/syncPlanList 失败';
                throw new Exception($msg);
            }
            $payload = $res['data'] ?? null;
            if (!is_array($payload)) {
                throw new Exception('entry backup/syncPlanList 返回 data 无效');
            }
            $plans = $payload['plans'] ?? [];
            return is_array($plans) ? $plans : [];
        });

        return is_array($resp) ? $resp : [];
    }
}
