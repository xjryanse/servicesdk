<?php
namespace xjryanse\servicesdk\wechat;

use xjryanse\phplite\facade\Cache;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\entry\EntrySdk;
/**
 * 公众号接入sdk
 */
class WepubSdk {
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_wechat';

    use \xjryanse\phplite\traits\InstMultiTrait;
    use \xjryanse\servicesdk\comm\traits\BindSdkTrait;
    
}
