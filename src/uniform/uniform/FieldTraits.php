<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform/field/* 万能表字段
 */
trait FieldTraits
{
    /**
     * POST uniform/field/searchFields
     *
     * @param array<string,mixed> $param table 必填
     */
    public function fieldSearchFields(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/field/searchFields', $param, $channel);
    }

    /**
     * POST uniform/field/tableFieldsArr
     *
     * @param array<string,mixed> $param table 必填
     */
    public function fieldTableFieldsArr(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/field/tableFieldsArr', $param, $channel);
    }
}
