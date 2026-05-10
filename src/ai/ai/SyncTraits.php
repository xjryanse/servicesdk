<?php
namespace xjryanse\servicesdk\ai\ai;

/**
 * 同步处理接口
 */
trait SyncTraits{
    
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
