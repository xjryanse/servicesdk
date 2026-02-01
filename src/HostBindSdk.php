<?php
namespace xjryanse\servicesdk;

use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 域名绑定sdk
 */
class HostBindSdk {
    /**
     * 2026年2月1日：公司id：租户id
     */
    public static function companyId(){
        global $svBindId;
        $info = EntrySdk::bindIdInfo($svBindId);
        return $info['bind_company_id'];
    }
}
