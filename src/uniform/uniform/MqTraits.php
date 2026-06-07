<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform/mq/* 万能表消息
 */
trait MqTraits
{
    /**
     * POST uniform/mq/push
     *
     * @param array<string,mixed> $param tableKey、operate、dataId 必填
     */
    public function mqPush(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/mq/push', $param, $channel);
    }

    /**
     * POST uniform/mq/uniformSync
     *
     * @param array<string,mixed> $param
     */
    public function mqUniformSync(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/mq/uniformSync', $param, $channel);
    }
}
