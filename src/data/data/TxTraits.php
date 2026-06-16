<?php
namespace xjryanse\servicesdk\data\data;

trait TxTraits{
    public function txCreate($bizType = '', $bizId = '', $ttl = 0, $txId = ''){
        $param = [
            'biz_type' => $bizType,
            'biz_id'   => $bizId,
            'dbId'     => $this->dbId,
            'svBindId' => $this->uuid,
        ];
        if ($ttl) {
            $param['ttl'] = $ttl;
        }
        if ($txId) {
            $param['txId'] = $txId;
        }

        $res = $this->queryLog('data/tx/create', $param, 'worker');
        return $res['data'];
    }

    public function txAdd($txId, $seq, $type, $table, $data = [], $where = []){
        $operation = [
            'seq'   => $seq,
            'type'  => $type,
            'table' => $table,
        ];
        if ($data !== []) {
            $operation['data'] = $data;
        }
        if ($where !== []) {
            $operation['where'] = $where;
        }

        $param = [
            'txId'      => $txId,
            'operation' => $operation,
            'dbId'      => $this->dbId,
            'svBindId'  => $this->uuid,
        ];

        $res = $this->queryLog('data/tx/add', $param, 'worker');
        return $res['data'];
    }

    public function txCommit($txId){
        $param = [
            'txId'     => $txId,
            'dbId'     => $this->dbId,
            'svBindId' => $this->uuid,
        ];

        $res = $this->queryLog('data/tx/commit', $param, 'worker');
        return $res['data'];
    }

    public function txExecute($bizType, $bizId, array $operations, $ttl = 0, $txId = ''){
        $param = [
            'biz_type'   => $bizType,
            'biz_id'     => $bizId,
            'operations' => $operations,
            'dbId'       => $this->dbId,
            'svBindId'   => $this->uuid,
        ];
        if ($ttl) {
            $param['ttl'] = $ttl;
        }
        if ($txId) {
            $param['txId'] = $txId;
        }

        $res = $this->queryLog('data/tx/execute', $param, 'worker');
        return $res['data'];
    }

    public function txStatus($txId){
        $param = [
            'txId'     => $txId,
            'dbId'     => $this->dbId,
            'svBindId' => $this->uuid,
        ];

        $res = $this->queryLog('data/tx/status', $param, 'worker');
        return $res['data'];
    }

    public function txCancel($txId){
        $param = [
            'txId'     => $txId,
            'dbId'     => $this->dbId,
            'svBindId' => $this->uuid,
        ];

        $res = $this->queryLog('data/tx/cancel', $param, 'worker');
        return $res['data'];
    }
}
