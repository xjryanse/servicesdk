<?php

namespace xjryanse\servicesdk\prize\prize;

use xjryanse\phplite\cache\NCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 计价规则接口（Worker + NCache）
 */
trait RuleTraits
{
    public function getPrizeWithFormula($time, $groupCate, $data, $con = [], $companyId = '')
    {
        $pMd5 = Arrays::md5([$time, $groupCate, $data, $con, $companyId, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($time, $groupCate, $data, $con, $companyId) {
            $baseUrl = 'prize/prize/getPrizeWithFormula';
            $post = array_merge($this->postBaseData(), [
                'time' => $time,
                'group_cate' => $groupCate,
                'data' => $data,
                'con' => $con,
                'company_id' => $companyId,
            ]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return $res;
    }

    public function getBaoPrizeWithFormula($data)
    {
        $pMd5 = Arrays::md5([$data, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($data) {
            $baseUrl = 'prize/prize/getBaoPrizeWithFormula';
            $post = array_merge($this->postBaseData(), ['data' => $data]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return $res;
    }

    public function getPerPrize($time, $groupCate, $data, $companyId = '')
    {
        $pMd5 = Arrays::md5([$time, $groupCate, $data, $companyId, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($time, $groupCate, $data, $companyId) {
            $baseUrl = 'prize/prize/getPerPrize';
            $post = array_merge($this->postBaseData(), [
                'time' => $time,
                'group_cate' => $groupCate,
                'data' => $data,
                'company_id' => $companyId,
            ]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });

        return $res;
    }

    public function getPrizeDataArr($groupId, $data)
    {
        $pMd5 = Arrays::md5([$groupId, $data, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($groupId, $data) {
            $baseUrl = 'prize/prize/getPrizeDataArr';
            $post = array_merge($this->postBaseData(), [
                'group_id' => $groupId,
                'data' => $data,
            ]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });

        return $res;
    }

    /**
     * 写操作，不走缓存
     */
/*
    public function setRuleRam($groupKey, $ruleInfo, $rules = [])
    {
        $baseUrl = 'prize/prize/setRuleRam';
        $post = array_merge($this->postBaseData(), [
            'group_key' => $groupKey,
            'rule_info' => $ruleInfo,
            'rules' => $rules,
        ]);
        $resp = $this->queryLog($baseUrl, $post, 'worker');
        return $resp['data'];
    }
*/

    public function prizeKeyToId($prizeKey)
    {
        $pMd5 = Arrays::md5([$prizeKey, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($prizeKey) {
            $baseUrl = 'prize/prize/keyToId';
            $post = array_merge($this->postBaseData(), ['prize_key' => $prizeKey]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return Arrays::value($res, 'id', '');
    }

    public function driverSalaryYing($data)
    {
        $pMd5 = Arrays::md5([$data, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($data) {
            $baseUrl = 'prize/prize/driverSalaryYing';
            $post = array_merge($this->postBaseData(), ['data' => $data]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return $res;
    }

    public function driverSalaryRate($data)
    {
        $pMd5 = Arrays::md5([$data, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($data) {
            $baseUrl = 'prize/prize/driverSalaryRate';
            $post = array_merge($this->postBaseData(), ['data' => $data]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return Arrays::value($res, 'rate', 0);
    }

    public function orderBaoBusRouteArea($data)
    {
        $pMd5 = Arrays::md5([$data, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($data) {
            $baseUrl = 'prize/prize/orderBaoBusRouteArea';
            $post = array_merge($this->postBaseData(), ['data' => $data]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return Arrays::value($res, 'routeArea');
    }
}
