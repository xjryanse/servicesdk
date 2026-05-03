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
     * 聊天补全
     */
    public function chatCompletions(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/chat/completions', $payload, $channel);
    }

    /**
     * 文本生成
     */
    public function generateText(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/generate/text', $payload, $channel);
    }

    /**
     * 图片生成
     */
    public function generateImage(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/generate/image', $payload, $channel);
    }

    /**
     * 视频生成
     */
    public function generateVideo(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/generate/video', $payload, $channel);
    }

    /**
     * 模型供应商列表
     */
    public function modelProviders(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/providers', $payload, $channel);
    }

    /**
     * 模型路由列表
     */
    public function modelRoutes(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/routes', $payload, $channel);
    }

    /**
     * 模型元信息
     */
    public function modelMeta(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/meta', $payload, $channel);
    }

    /**
     * 新增模型路由
     */
    public function modelCreate(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/create', $payload, $channel);
    }

    /**
     * 更新模型路由
     */
    public function modelUpdate(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/update', $payload, $channel);
    }

    /**
     * 删除模型路由
     */
    public function modelDelete(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/model/delete', $payload, $channel);
    }

    /**
     * provider 配置列表
     */
    public function providerConfig(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/provider/config', $payload, $channel);
    }

    /**
     * 更新 provider 配置
     */
    public function providerUpdate(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/provider/update', $payload, $channel);
    }

    /**
     * 新增 provider
     */
    public function providerCreate(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/provider/create', $payload, $channel);
    }

    /**
     * 删除 provider
     */
    public function providerDelete(array $payload = [], string $channel = 'curl') {
        return $this->requestData('ai/provider/delete', $payload, $channel);
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
