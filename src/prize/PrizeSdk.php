<?php

namespace xjryanse\servicesdk\prize;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * service_prize 计价规则微服务 SDK（默认 Worker TCP 调用）
 */
class PrizeSdk extends SdkBase
{
    use \xjryanse\servicesdk\prize\prize\RuleTraits;
    use \xjryanse\servicesdk\prize\prize\CircuitTraits;

    protected static $serverKey = 'service_prize';

}
