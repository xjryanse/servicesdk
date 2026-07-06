<?php

namespace xjryanse\servicesdk\entry;

use xjryanse\servicesdk\comm\TcpRetry;
use xjryanse\servicesdk\comm\TcpCtx;
use xjryanse\phplite\logic\Arrays;
use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Env;
use Exception;

/**
 * service_entry 调用方 SDK（TCP Worker + SCache）
 */
class EntrySdk
{
    use \xjryanse\servicesdk\entry\backup\BackupTraits;
    use \xjryanse\servicesdk\entry\db\DbTraits;
    use \xjryanse\servicesdk\entry\phpfpm\HostTraits;
    use \xjryanse\servicesdk\entry\svBind\SvBindTraits;

    public static function sdkIp()
    {
        return Env::value('ServiceEntryHost') ?: '127.0.0.1';
    }

    protected static function workerPort(): string
    {
        $port = Env::value('ServiceEntryWorkerPort');
        return ($port !== null && $port !== '') ? (string) $port : '19919';
    }

    /**
     * @param array<string,mixed> $param
     * @return array<string,mixed>|null
     */
    protected static function wQuery(string $baseUrl, array $param = [])
    {
        $qParam = TcpCtx::envelope($baseUrl, $param);
        return TcpRetry::syncRequest(static::sdkIp(), static::workerPort(), $qParam);
    }

    public static function hostBindInfo($host)
    {
        if ($host === '127.0.0.1') {
            throw new Exception('不支持的域名' . $host);
        }
        $cacheKey = static::generateCacheKey('hostBindInfo', $host);
        return SCache::funcGet($cacheKey, function () use ($host) {
            $res = static::wQuery('entry/host/bindInfo', ['host' => $host]);
            return static::parseEntryDataNullable($res, 'host/bindInfo');
        });
    }

    /**
     * @throws Exception
     */
    public static function bindIdInfo($bindId)
    {
        if (!$bindId) {
            throw new Exception('$bindId必须');
        }
        if (!is_numeric($bindId)) {
            throw new Exception('不支持的绑定id格式');
        }

        $cacheKey = static::generateCacheKey('bindIdInfo', $bindId);
        return SCache::funcGet($cacheKey, function () use ($bindId) {
            $res = static::wQuery('entry/host/bindIdInfo', ['bindId' => $bindId]);
            return static::parseEntryData($res, 'host/bindIdInfo');
        });
    }

    /**
     * @throws Exception
     */
    public static function companyKeyInfo($key)
    {
        if (mb_strlen($key) !== 8) {
            throw new Exception('不是合法租户key');
        }

        $cacheKey = static::generateCacheKey('companyKeyInfo', $key);
        return SCache::funcGet($cacheKey, function () use ($key) {
            $res = static::wQuery('entry/company/keyInfo', ['key' => $key]);
            return static::parseEntryDataNullable($res, 'company/keyInfo');
        });
    }

    /**
     * @throws Exception
     */
    public static function companyIdInfo($id)
    {
        $cacheKey = static::generateCacheKey('companyIdInfo', $id);
        return SCache::funcGet($cacheKey, function () use ($id) {
            $res = static::wQuery('entry/company/info', ['id' => $id]);
            return static::parseEntryDataNullable($res, 'company/info');
        });
    }

    public static function hostCovMap()
    {
        $cacheKey = static::generateCacheKey('hostCovMap');
        return SCache::funcGet($cacheKey, function () {
            $res = static::wQuery('entry/hostCov/map', []);
            return static::parseEntryData($res, 'hostCov/map');
        });
    }

    public static function serverList($bindId, $serverKey): array
    {
        $info = static::bindIdInfo($bindId);
        $servers = Arrays::value($info, 'servers') ?: [];
        return Arrays::value($servers, $serverKey) ?: [];
    }

    protected static function generateCacheKey(string $method, $subFix = null): string
    {
        $key = __CLASS__ . '::' . $method . ':' . md5(static::sdkIp());
        if ($subFix !== null) {
            $key .= ':' . $subFix;
        }
        return $key;
    }

    public static function clearCache($method, $key = null)
    {
        SCache::rm(static::generateCacheKey($method, $key));
    }

    /**
     * @param mixed $res
     * @return array<string,mixed>
     * @throws Exception
     */
    protected static function parseEntryData($res, string $action): array
    {
        if (!is_array($res) || !array_key_exists('code', $res)) {
            throw new Exception('entry/' . $action . ' 响应无效');
        }
        if ((int) $res['code'] !== 0) {
            $msg = isset($res['message']) ? (string) $res['message'] : '失败';
            throw new Exception('entry:' . $msg);
        }
        $data = $res['data'] ?? null;
        if (!is_array($data)) {
            throw new Exception('entry/' . $action . ' 返回 data 无效');
        }
        return $data;
    }

    /**
     * @param mixed $res
     * @return array<string,mixed>|null
     */
    protected static function parseEntryDataNullable($res, string $action): ?array
    {
        if (!is_array($res) || (int) ($res['code'] ?? 1) !== 0) {
            return null;
        }
        $data = $res['data'] ?? null;
        return is_array($data) ? $data : null;
    }
}
