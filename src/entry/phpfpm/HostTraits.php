<?php

namespace xjryanse\servicesdk\entry\phpfpm;

use xjryanse\phplite\facade\Request;
/**
 * 缓存类
 */
trait HostTraits {
    /**
     * phpfpm环境下
     */
    public static function currentHostBindId(){
        $host       = Request::host();
        $bindInfo   = static::hostBindInfo($host);
        return $bindInfo ? $bindInfo['id'] : '';
    }

}

