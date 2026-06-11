<?php

namespace xjryanse\servicesdk\message;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 消息中台 service_message 接入 SDK
 */
class MessageSdk extends SdkBase
{
    protected static $serverKey = 'service_message';

    /**
     * 统一推送入口：提交消息任务（组装并入队）
     * POST /message/task/submit
     *
     * @param array  $param   bizId、tplCode、rawData 必填；channels、idempotentKey 可选
     * @param string $channel curl|worker，默认 curl
     * @return mixed taskIds、tasks 等，见中台接口 data
     */
    public function submit(array $param, $channel = 'curl')
    {
        $baseUrl = 'message/task/submit';
        $data = array_merge($this->postBaseData(), $param);
        $res = $this->queryLog($baseUrl, $data, $channel);
        return $res['data'];
    }
}
