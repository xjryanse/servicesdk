<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform/record/* 万能表记录 CRUD
 */
trait RecordTraits
{
    /**
     * POST uniform/record/get
     *
     * @param array<string,mixed> $param table、id 必填
     */
    public function recordGet(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/get', $param, $channel);
    }

    /**
     * POST uniform/record/save
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordSave(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/save', $param, $channel);
    }

    /**
     * POST uniform/record/update
     *
     * @param array<string,mixed> $param table、id 必填
     */
    public function recordUpdate(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/update', $param, $channel);
    }

    /**
     * POST uniform/record/del
     *
     * @param array<string,mixed> $param table、id 必填
     */
    public function recordDel(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/del', $param, $channel);
    }

    /**
     * POST uniform/record/paginate
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordPaginate(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/paginate', $param, $channel);
    }

    /** tenancy 兼容别名 uniform/record/pgList */
    public function recordPgList(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/pgList', $param, $channel);
    }

    /**
     * POST uniform/record/list
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordList(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/list', $param, $channel);
    }

    /**
     * POST uniform/record/count
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordCount(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/count', $param, $channel);
    }

    /**
     * POST uniform/record/saveAll
     *
     * @param array<string,mixed> $param table、table_data 必填
     */
    public function recordSaveAll(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/saveAll', $param, $channel);
    }

    /**
     * POST uniform/record/saveWithDtl
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordSaveWithDtl(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/saveWithDtl', $param, $channel);
    }

    /**
     * POST uniform/record/uniqueSave
     *
     * @param array<string,mixed> $param table 必填
     */
    public function recordUniqueSave(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/uniqueSave', $param, $channel);
    }

    /**
     * POST uniform/record/cancelWithFinanceRef
     *
     * @param array<string,mixed> $param table、id 必填
     */
    public function recordCancelWithFinanceRef(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/cancelWithFinanceRef', $param, $channel);
    }

    /**
     * POST uniform/record/getByUser
     *
     * @param array<string,mixed> $param table、we_pub_openid 必填
     */
    public function recordGetByUser(array $param, $channel = 'worker')
    {
        return $this->uniformRequest('uniform/record/getByUser', $param, $channel);
    }
}
