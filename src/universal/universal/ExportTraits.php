<?php
namespace xjryanse\servicesdk\universal\universal;

use xjryanse\phplite\curl\Query;

/**
 * 导出字段配置与转换（service_universal）
 */
trait ExportTraits
{
    /**
     * POST universal/export/fields
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function exportFields(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $url  = $this->sdkUrl('universal/export/fields');
        $res  = Query::posturl($url, $data);
        return $this->exportAssertOk($res, $url);
    }

    /**
     * POST universal/export/pack
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function exportPack(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $url  = $this->sdkUrl('universal/export/pack');
        $res  = Query::posturl($url, $data);
        return $this->exportAssertOk($res, $url);
    }

    /**
     * POST universal/export/deal
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function exportDeal(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $url  = $this->sdkUrl('universal/export/deal');
        $res  = Query::posturl($url, $data);
        return $this->exportAssertOk($res, $url);
    }

    /**
     * @param array<string,mixed>|null $res
     * @return array<string,mixed>
     */
    private function exportAssertOk($res, string $url): array
    {
        if (!$res || !is_array($res)) {
            throw new \Exception('service_universal 无响应: ' . $url);
        }
        if ((int) ($res['code'] ?? 1) !== 0) {
            throw new \Exception((string) ($res['message'] ?? 'service_universal 返回错误') . ' url=' . $url);
        }
        $data = $res['data'] ?? null;
        return is_array($data) ? $data : [];
    }
}
