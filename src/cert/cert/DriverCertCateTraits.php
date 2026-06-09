<?php

namespace xjryanse\servicesdk\cert\cert;

use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 驾驶证准驾车型代号 w_driver_cert_cate
 */
trait DriverCertCateTraits
{
    public function driverCertCateSave(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        $res = $this->certRequest('cert/driver_cert_cate/save', $param, $channel);
        return Arrays::value($res, 'id', $res);
    }

    public function driverCertCateUpdate(array $data, $channel = 'curl')
    {
        $param = Arrays::value($data, 'table_data') ? $data : ['table_data' => $data];
        return $this->certRequest('cert/driver_cert_cate/update', $param, $channel);
    }

    public function driverCertCateGet($id, $channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, $id);
        $res = SCache::funcGet($cacheKey, function () use ($id, $channel) {
            return $this->certRequest('cert/driver_cert_cate/get', ['id' => $id], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }

    public function driverCertCatePaginate(array $param, $channel = 'worker')
    {
        return $this->certRequest('cert/driver_cert_cate/paginate', $param, $channel);
    }

    public function driverCertCateDelete($id, $channel = 'curl')
    {
        return $this->certRequest('cert/driver_cert_cate/delete', ['id' => $id], $channel);
    }

    /**
     * 启用中的准驾车型列表
     */
    public function driverCertCateEnabledList($channel = 'worker')
    {
        $cacheKey = $this->certCacheKey(__FUNCTION__, 'all');
        $res = SCache::funcGet($cacheKey, function () use ($channel) {
            return $this->certRequest('cert/driver_cert_cate/enabledList', [], $channel);
        });
        if (!$res) {
            SCache::rm($cacheKey);
        }
        return $res;
    }
}
