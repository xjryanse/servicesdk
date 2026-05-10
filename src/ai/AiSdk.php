<?php
namespace xjryanse\servicesdk\ai;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * service_ai 对接 SDK
 */
class AiSdk extends SdkBase {
    use \xjryanse\servicesdk\ai\ai\SyncTraits;
    use \xjryanse\servicesdk\ai\ai\AsyncTraits;
    // 配套 BindSdkTrait 使用
    protected static $serverKey = 'service_ai';

    // 当前项目 service_ai 的 phpfpm 端口
    protected function sdkPort() {
        return 9928;
    }

    // 当前项目 service_ai 的 workerman 端口
    protected function workerPort() {
        return 19928;
    }
}