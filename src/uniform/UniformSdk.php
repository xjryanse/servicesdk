<?php

namespace xjryanse\servicesdk\uniform;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * service_uniform 万能表微服务 SDK
 */
class UniformSdk extends SdkBase
{
    protected static $serverKey = 'service_uniform';

    use \xjryanse\servicesdk\uniform\uniform\RequestTraits;
    use \xjryanse\servicesdk\uniform\uniform\RecordTraits;
    use \xjryanse\servicesdk\uniform\uniform\TableTraits;
    use \xjryanse\servicesdk\uniform\uniform\FieldTraits;
    use \xjryanse\servicesdk\uniform\uniform\DataTraits;
    use \xjryanse\servicesdk\uniform\uniform\MqTraits;
}
