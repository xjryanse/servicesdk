<?php
namespace xjryanse\servicesdk\finance;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 
 */
class FinanceSdk extends SdkBase{
    use \xjryanse\servicesdk\finance\finance\BelongTableTraits;
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_finance';

    


}
