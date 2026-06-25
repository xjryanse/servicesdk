<?php

namespace xjryanse\servicesdk\entry\phpfpm;

use xjryanse\phplite\facade\Request;
use Exception;

trait HostTraits
{
    /**
     * @return array<string,mixed>
     * @throws Exception
     */
    private static function requireCurrentHostBindInfo(): array
    {
        $host = Request::host();
        $bindInfo = static::hostBindInfo($host);
        if (!$bindInfo) {
            throw new Exception(static::sdkIp() . '没有配置域名绑定信息' . $host);
        }
        return $bindInfo;
    }

    public static function currentHostBindId()
    {
        return static::requireCurrentHostBindInfo()['id'];
    }

    public static function currentHostBindInfo()
    {
        return static::requireCurrentHostBindInfo();
    }
}
