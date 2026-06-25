<?php

namespace xjryanse\servicesdk;

use xjryanse\servicesdk\entry\EntrySdk;
use Exception;

/**
 * 域名绑定 sdk（委托 EntrySdk）
 */
class HostBindSdk
{
    /**
     * @throws Exception
     */
    public static function companyId()
    {
        global $svBindId;
        if (!$svBindId) {
            throw new Exception('$svBindId必须');
        }
        return EntrySdk::globalSvBindCompanyId();
    }
}
