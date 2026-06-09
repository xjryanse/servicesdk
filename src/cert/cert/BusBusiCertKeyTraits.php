<?php

namespace xjryanse\servicesdk\cert\cert;

use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 车辆运营类型证件 key 配置 w_bus_busi_cert_key
 */
trait BusBusiCertKeyTraits
{
    public function busBusiCertKeySave(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        $res = $this->certRequest('cert/bus_busi_cert_key/save', $param, $channel);
        return Arrays::value($res, 'id', $res);
    }

    public function busBusiCertKeyUpdate(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        return $this->certRequest('cert/bus_busi_cert_key/update', $param, $channel);
    }

    public function busBusiCertKeyGet($id, $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $id);
        $res = SCache::funcGet($cacheKey, function () use ($id, $channel) {
            return $this->certRequest('cert/bus_busi_cert_key/get', ['id' => $id], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }

    public function busBusiCertKeyPaginate(array $param, $channel = 'worker')
    {
        return $this->certRequest('cert/bus_busi_cert_key/paginate', $param, $channel);
    }

    public function busBusiCertKeyDelete($id, $channel = 'curl')
    {
        return $this->certRequest('cert/bus_busi_cert_key/delete', ['id' => $id], $channel);
    }

    /**
     * 按运营类型查询证件 key 列表
     */
    public function keysByBusiType($busiType, $certType = '', $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $busiType . '_' . $certType);
        $res = SCache::funcGet($cacheKey, function () use ($busiType, $certType, $channel) {
            $param = ['busi_type' => $busiType];
            if ($certType !== '') {
                $param['cert_type'] = $certType;
            }
            return $this->certRequest('cert/bus_busi_cert_key/keysByBusiType', $param, $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }
}
