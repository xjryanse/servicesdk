<?php

namespace xjryanse\servicesdk\prize\prize;

use xjryanse\phplite\cache\NCache;
use xjryanse\phplite\logic\Arrays;

/**
 * 线路票价接口（Worker + NCache）
 */
trait CircuitTraits
{
    public function circuitGetPrize($circuitId, $fromCircuitStationId, $toCircuitStationId)
    {
        $pMd5 = Arrays::md5([$circuitId, $fromCircuitStationId, $toCircuitStationId, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($circuitId, $fromCircuitStationId, $toCircuitStationId) {
            $baseUrl = 'prize/circuit/getPrize';
            $post = array_merge($this->postBaseData(), [
                'circuit_id' => $circuitId,
                'from_circuit_station_id' => $fromCircuitStationId,
                'to_circuit_station_id' => $toCircuitStationId,
            ]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });

        return Arrays::value($res, 'prize', 0);
    }

    public function circuitGetPrizeBatch($items)
    {
        $pMd5 = Arrays::md5([$items, $this->uuid]);
        $key = __CLASS__ . __METHOD__ . $pMd5;
        $res = NCache::funcGet($key, function () use ($items) {
            $baseUrl = 'prize/circuit/getPrizeBatch';
            $post = array_merge($this->postBaseData(), ['items' => $items]);
            $resp = $this->queryLog($baseUrl, $post, 'worker');
            return $resp['data'];
        });
        return $res;
    }
}
