<?php

namespace xjryanse\servicesdk\ws;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * WebSocket 推送 service_ws 接入 SDK
 */
class WsSdk extends SdkBase
{
    protected static $serverKey = 'service_ws';

    /**
     * 推送站内信等事件到在线用户
     * POST ws/push/dispatch
     *
     * @param array $param event, data, userIds 或 scopes, svBindId
     */
    public function pushDispatch(array $param, $channel = 'worker')
    {
        $baseUrl = 'ws/push/dispatch';
        $data    = array_merge($this->postBaseData(), $param);
        $res     = $this->queryLog($baseUrl, $data, $channel);
        return $res['data'];
    }

    /**
     * 连接统计
     */
    public function connStats($channel = 'worker')
    {
        $res = $this->queryLog('ws/conn/stats', $this->postBaseData(), $channel);
        return $res['data'];
    }
}
