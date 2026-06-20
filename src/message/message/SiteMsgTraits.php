<?php

namespace xjryanse\servicesdk\message\message;

use Exception;

/**
 * service_message 站内信 siteMsg/*
 */
trait SiteMsgTraits
{
    /**
     * POST /message/siteMsg/list
     *
     * @param string $sessionUserId 网关鉴权后透传
     * @param array  $extra         onlyUnread、limit、roleIds、deptIds、source、adminType
     */
    public function siteMsgList(string $sessionUserId, array $extra = [], $channel = 'worker')
    {
        return $this->siteMsgRequest('list', $this->siteMsgViewerParam($sessionUserId, $extra), $channel);
    }

    /**
     * POST /message/siteMsg/get
     */
    public function siteMsgGet(string $sessionUserId, $id, array $extra = [], $channel = 'worker')
    {
        $data = array_merge($this->siteMsgViewerParam($sessionUserId, $extra), ['id' => $id]);
        return $this->siteMsgRequest('get', $data, $channel);
    }

    /**
     * POST /message/siteMsg/read
     */
    public function siteMsgRead(string $sessionUserId, $id, array $extra = [], $channel = 'worker')
    {
        $data = array_merge($this->siteMsgViewerParam($sessionUserId, $extra), ['id' => $id]);
        return $this->siteMsgRequest('read', $data, $channel);
    }

    /**
     * POST /message/siteMsg/readAll
     */
    public function siteMsgReadAll(string $sessionUserId, array $extra = [], $channel = 'worker')
    {
        return $this->siteMsgRequest('readAll', $this->siteMsgViewerParam($sessionUserId, $extra), $channel);
    }

    /**
     * POST /message/siteMsg/internalCreate（运营/测试，不经 MsgTask）
     *
     * @param array $param title、content、details、scopes、tplCode、bizId、publishTime...
     */
    public function siteMsgInternalCreate(array $param, $channel = 'worker')
    {
        $data = array_merge($this->postBaseData(), $param);
        return $this->siteMsgRequest('internalCreate', $data, $channel);
    }

    /**
     * 读接口公共参数
     */
    protected function siteMsgViewerParam(string $sessionUserId, array $extra = []): array
    {
        if ($sessionUserId === '') {
            throw new Exception('sessionUserId必须');
        }
        return array_merge($this->postBaseData(), ['sessionUserId' => (string) $sessionUserId], $extra);
    }

    /**
     * 统一请求 siteMsg 子路由
     */
    protected function siteMsgRequest(string $action, array $data, $channel = 'worker')
    {
        $res = $this->queryLog('message/siteMsg/' . $action, $data, $channel);
        return $res['data'];
    }
}
