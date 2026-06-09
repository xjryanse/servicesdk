<?php

namespace xjryanse\servicesdk\cert;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 证件管理微服务 SDK
 * HTTP 9938 / Worker 19938
 */
class CertSdk extends SdkBase
{
    use \xjryanse\servicesdk\cert\cert\RequestTraits;
    use \xjryanse\servicesdk\cert\cert\CertTraits;
    use \xjryanse\servicesdk\cert\cert\BusBusiCertKeyTraits;
    use \xjryanse\servicesdk\cert\cert\DriverCertCateTraits;
    use \xjryanse\servicesdk\cert\cert\MqTraits;

    /** @var string 入口库 server_key，须与运维登记一致 */
    protected static $serverKey = 'service_cert';
}
