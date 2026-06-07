<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform/table/* 万能表元数据
 */
trait TableTraits
{
    /**
     * POST uniform/table/getByTableNo
     *
     * @param array<string,mixed> $param table 必填
     */
    public function tableGetByTableNo(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/table/getByTableNo', $param, $channel);
    }

    /**
     * POST uniform/table/calTableSql
     *
     * @param array<string,mixed> $param table 必填
     */
    public function tableCalTableSql(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/table/calTableSql', $param, $channel);
    }
}
