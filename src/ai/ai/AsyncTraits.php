<?php
namespace xjryanse\servicesdk\ai\ai;

/**
 *异步处理接口
 */
trait AsyncTraits{
    
    /**
     * 文本生成
     */
    public function generateAsyncText(array $param = [], string $channel = 'curl') {
        $baseUrl    = 'ai/generate_async/text';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data   = $this->postBaseData();
        $qParam = array_merge($param, $data);
        $res    = $this->queryLog($baseUrl, $qParam, $channel);
        return $res['data'];
    }

}
