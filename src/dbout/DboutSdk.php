<?php

namespace xjryanse\servicesdk\dbout;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * service_dbout 微服务 SDK（经 EntrySdk::serverList(bindId, service_dbout) 发现节点）
 */
class DboutSdk extends SdkBase
{
    protected static $serverKey = 'service_dbout';

    use \xjryanse\servicesdk\dbout\export\ExportTraits;

    /** @var string */
    protected $backupToken = '';

    public function setBackupToken(string $token): self
    {
        $this->backupToken = trim($token);
        return $this;
    }

    protected function sdkUrl($path): string
    {
        $path = ltrim((string) $path, '/');
        return 'http://' . $this->sdkIp() . ':' . $this->sdkPort() . '/public/' . $path;
    }
}
