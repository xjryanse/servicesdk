<?php
namespace xjryanse\servicesdk\ai;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * service_ai 对接 SDK
 */
class AiSdk extends SdkBase {
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

    /**
     * 文本生成
     */
    public function generateText(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/generate/text', $payload, $channel);
    }

    /**
     * 通用请求入口：返回服务端 data 字段
     */
    protected function requestData(string $baseUrl, array $payload = [], string $channel = 'curl') {
        $data = array_merge($this->postBaseData(), $payload);
        $res = $this->queryLog($baseUrl, $data, $channel);
        return isset($res['data']) ? $res['data'] : null;
    }
}
