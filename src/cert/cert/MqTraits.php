<?php

namespace xjryanse\servicesdk\cert\cert;

/**
 * 证件异步 MQ 入口
 */
trait MqTraits
{
    /**
     * MQ：批量初始化证件占位
     *
     * @param array<string,mixed> $param belong_table、belong_table_id、keys
     */
    public function mqCertInit(array $param, $channel = 'worker')
    {
        return $this->certRequest('cert/mq/certInit', $param, $channel);
    }
}
