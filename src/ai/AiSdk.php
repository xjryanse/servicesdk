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
    public function generateText(array $param = [], string $channel = 'curl') {
        $baseUrl    = 'ai/generate/text';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data   = $this->postBaseData();
        $qParam = array_merge($param, $data);
        $res    = $this->queryLog($baseUrl, $qParam, $channel);
        return $res['data'];
    }
}
