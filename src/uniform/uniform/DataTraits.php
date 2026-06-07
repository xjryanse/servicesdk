<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform/data/* 制服/绩效数据同步
 */
trait DataTraits
{
    /**
     * 从 SQL 中台拉取数据并批量写入制服表
     *
     * POST uniform/data/batchAdd
     * 必填：sqlKey、id（逗号分隔或数组）
     *
     * @param array<string,mixed> $param
     */
    public function dataBatchAdd(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/data/batchAdd', $param, $channel);
    }
}
