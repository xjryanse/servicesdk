<?php

namespace xjryanse\servicesdk\entry\backup;

use xjryanse\phplite\cache\SCache;
use Exception;

/**
 * 备份配置：从 service_entry 拉取 w_db_backup_profile（TCP entry/backup/profile）
 */
trait BackupTraits {

    /**
     * 按 bindId + role 获取启用的备份 profile 配置（dbin / dbout）
     *
     * @param string $bindId 入口绑定 id（svBindId）
     * @param string $role dbin|dbout
     * @return array<string,mixed>
     * @throws Exception
     */
    public static function backupProfile(string $bindId, string $role = 'dbin'): array {
        $bindId = trim($bindId);
        $role = strtolower(trim($role)) ?: 'dbin';
        if ($bindId === '') {
            throw new Exception('bindId 不能为空');
        }
        if (!is_numeric($bindId)) {
            throw new Exception('不支持的绑定id格式');
        }

        $cacheKey = static::generateCacheKey(__FUNCTION__, $role . '_' . $bindId);
        $resp = SCache::funcGet($cacheKey, function () use ($bindId, $role) {
            $baseUrl = 'entry/backup/profile';
            $data = [
                'bindId' => $bindId,
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
     * @return list<string>
     * @throws Exception
     */
    public static function backupSyncBindList(): array
    {
        $cacheKey = static::generateCacheKey(__FUNCTION__, 'dbin');
        $resp = SCache::funcGet($cacheKey, function () {
            $res = static::wQuery('entry/backup/syncBindList', []);
            if (!is_array($res) || (int) ($res['code'] ?? 1) !== 0) {
                $msg = is_array($res) && isset($res['message'])
                    ? (string) $res['message']
                    : 'entry backup/syncBindList 失败';
                throw new Exception($msg);
            }
            $payload = $res['data'] ?? null;
            if (!is_array($payload)) {
                throw new Exception('entry backup/syncBindList 返回 data 无效');
            }
            $ids = $payload['bind_ids'] ?? [];
            if (!is_array($ids)) {
                return [];
            }
            $out = [];
            foreach ($ids as $id) {
                $s = trim((string) $id);
                if ($s !== '') {
                    $out[] = $s;
                }
            }
            return $out;
        });

        return is_array($resp) ? $resp : [];
    }
}
