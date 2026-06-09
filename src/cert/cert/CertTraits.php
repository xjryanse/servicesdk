<?php

namespace xjryanse\servicesdk\cert\cert;

use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 证件表 w_cert
 */
trait CertTraits
{
    /**
     * 保存（新增/更新）
     *
     * @param array<string,mixed> $data
     * @return string id
     */
    public function save(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        $res = $this->certRequest('cert/cert/save', $param, $channel);
        return Arrays::value($res, 'id', $res);
    }

    /**
     * 更新
     *
     * @param array<string,mixed> $data
     */
    public function update(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        return $this->certRequest('cert/cert/update', $param, $channel);
    }

    /**
     * 按 id 查询
     */
    public function get($id, $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $id);
        $res = SCache::funcGet($cacheKey, function () use ($id, $channel) {
            return $this->certRequest('cert/cert/get', ['id' => $id], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }

    /**
     * 分页列表（实时，不缓存）
     *
     * @param array<string,mixed> $param
     */
    public function paginate(array $param, $channel = 'worker')
    {
        return $this->certRequest('cert/cert/paginate', $param, $channel);
    }

    /**
     * 删除
     */
    public function delete($id, $channel = 'curl')
    {
        return $this->certRequest('cert/cert/delete', ['id' => $id], $channel);
    }

    /**
     * 归属 + key 获取或创建 id
     */
    public function belongIdKeyGetId($belongTable, $belongTableId, $certKey, $channel = 'worker')
    {
        $res = $this->certRequest('cert/cert/belongIdKeyGetId', [
            'belong_table'    => $belongTable,
            'belong_table_id' => $belongTableId,
            'cert_key'        => $certKey,
        ], $channel);
        return Arrays::value($res, 'id', $res);
    }

    /**
     * 获取证件编号（兼容 tenancy CertService::getCertNo）
     */
    public function getCertNo($certKey, $belongTableId, $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $certKey . '_' . $belongTableId);
        $certNo = SCache::funcGet($cacheKey, function () use ($certKey, $belongTableId, $channel) {
            $res = $this->certRequest('cert/cert/certNo', [
                'cert_key'        => $certKey,
                'belong_table_id' => $belongTableId,
            ], $channel);
            return Arrays::value($res, 'cert_no', '');
        });
        if ($certNo === '' || $certNo === null) {
            SCache::rm($cacheKey);
        }
        return $certNo ?: '';
    }

    /**
     * 批量初始化证件占位
     *
     * @param string   $belongTable
     * @param string   $belongTableId
     * @param string[] $keys
     */
    public function certInit($belongTable, $belongTableId, array $keys, $channel = 'curl')
    {
        return $this->certRequest('cert/cert/certInit', [
            'belong_table'    => $belongTable,
            'belong_table_id' => $belongTableId,
            'keys'            => $keys,
        ], $channel);
    }

    /**
     * 司机驾驶证准驾车型
     */
    public function driverCertLevel($userId, $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $userId);
        $level = SCache::funcGet($cacheKey, function () use ($userId, $channel) {
            $res = $this->certRequest('cert/cert/driverCertLevel', ['user_id' => $userId], $channel);
            return Arrays::value($res, 'cert_level', '');
        });
        if ($level === '' || $level === null) {
            SCache::rm($cacheKey);
        }
        return $level ?: '';
    }

    /**
     * 补充到期状态与剩余天数
     *
     * @param string[]|string $ids
     */
    public function extraDetails($ids, $channel = 'worker')
    {
        $idList = is_array($ids) ? $ids : array_filter(array_map('trim', explode(',', (string) $ids)));
        $cacheKey = $this->certCacheKey(__FUNCTION__, Arrays::md5($idList));
        $res = SCache::funcGet($cacheKey, function () use ($idList, $channel) {
            return $this->certRequest('cert/cert/extraDetails', ['ids' => $idList], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }

    /**
     * 条件列表
     *
     * @param array  $con
     * @param string $orderBy
     */
    public function conList(array $con = [], $orderBy = '', $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, Arrays::md5([$con, $orderBy]));
        $res = SCache::funcGet($cacheKey, function () use ($con, $orderBy, $channel) {
            return $this->certRequest('cert/cert/conList', [
                'con'      => $con,
                'order_by' => $orderBy,
            ], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }
}
