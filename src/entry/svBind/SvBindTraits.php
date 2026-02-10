<?php

namespace xjryanse\servicesdk\entry\svBind;

/**
 * 缓存类
 */
trait SvBindTraits {

    /**
     * 2026年2月1日：phpfpm环境下
     */
    public static function globalSvBindCompanyId(){
        global $svBindId;
        if(!$svBindId){
            return null;
        }
        $svBindInfo = static::bindIdInfo($svBindId);
        if(!$svBindInfo){
            throw new Exception('没有获取到绑定信息'.$svBindId);
        }

        return $svBindInfo['bind_company_id'];
    }
}

