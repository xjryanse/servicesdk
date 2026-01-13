<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\speedy\facade\Cache;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 公众号接入sdk
 */
class WepubSdk {

    protected static function sdkIp(){
        return EntrySdk::serveIp();
    }
    
    protected static function sdkPort(){
        return '9908';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }

    
}
