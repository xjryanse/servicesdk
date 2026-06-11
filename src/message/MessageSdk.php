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
    public function submit($dataId, $msgTplCode, array $param, $msgChannel = [], $channel = 'curl')
    {
        $qData = [
            'bizId'         => $dataId,
            'tplCode'       => $msgTplCode,
            'rawData'       => $param,
            'channels'      => $msgChannel,  // 可选，不传则用模板已绑定渠道
            // 防止同一条业务消息被重复提交、重复推送的唯一键
            // 'idempotentKey' => 'warehouse_out_20260611001:WAREHOUSE_OUT_NOTICE:webhook',  // 可选
        ];
        
        $baseUrl = 'message/task/submit';
        $data = array_merge($this->postBaseData(), $qData);
        $res = $this->queryLog($baseUrl, $data, $channel);
        return $res['data'];
    }
}
