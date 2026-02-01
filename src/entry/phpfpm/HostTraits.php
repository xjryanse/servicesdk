<?php

namespace xjryanse\servicesdk\entry\phpfpm;

use xjryanse\phplite\facade\Request;
use Exception;
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
        if(!$bindInfo){
            throw new Exception('没有配置域名绑定信息'.$host);
        }
        return $bindInfo ? $bindInfo['id'] : '';
    }

    /**
     * 2026年2月1日：phpfpm环境下
     */
    public static function currentHostBindInfo(){
        $host       = Request::host();
        $bindInfo   = static::hostBindInfo($host);
        if(!$bindInfo){
            throw new Exception('没有配置域名绑定信息'.$host);
        }
        return $bindInfo;
    }    
}

