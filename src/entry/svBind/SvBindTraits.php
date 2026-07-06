<?php

namespace xjryanse\servicesdk\entry\svBind;

use Exception;

trait SvBindTraits
{
    public static function globalSvBindCompanyId()
    {
        global $svBindId;
        if (!$svBindId) {
            return null;
        }
        $svBindInfo = static::bindIdInfo($svBindId);
        if (!$svBindInfo) {
            throw new Exception('没有获取到绑定信息' . $svBindId);
        }
        return $svBindInfo['bind_company_id'];
    }
}
