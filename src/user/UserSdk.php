<?php
namespace xjryanse\servicesdk\user;

use xjryanse\servicesdk\comm\SdkBase;
/**
 * 公众号接入sdk
 */
class UserSdk extends SdkBase{
    // 登录相关
    use \xjryanse\servicesdk\user\user\IdNoTraits;
    // 身份证号码相关
    use \xjryanse\servicesdk\user\user\LoginTraits;
    // 用户信息获取相关
    use \xjryanse\servicesdk\user\user\InfoTraits;
    
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_user';



}
