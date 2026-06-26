<?php
namespace xjryanse\servicesdk\export;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\phplite\curl\Query;
use Exception;

/**
 * 导出微服务 SDK（service_export）
 */
class ExportSdk extends SdkBase{
    protected static $serverKey = 'service_export';

    /**
     * 全链路导出编排
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function pipelineRun(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $baseUrl    = 'export/pipeline/run';
        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];

    }

    /**
     * 已转换数据 → 文件（自动选 Excel/CSV）
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function tableSync(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $url  = $this->sdkUrl('export/table/sync');
        $res  = Query::posturl($url, $data);
        $this->assertOk($res, $url);
        return $res['data'];
    }

    /**
     * 同步导出 excel
     */
    public function excelSync($exportData, $dataTitle)
    {
        return $this->tableSync([
            'exportData'   => $exportData,
            'dataTitle'    => $dataTitle,
            'excelMaxRows' => PHP_INT_MAX,
        ]);
    }

    /**
     * 同步导出 csv
     */
    public function csvSync($exportData, $dataTitle)
    {
        $data = array_merge($this->postBaseData(), [
            'exportData' => $exportData,
            'dataTitle'  => $dataTitle,
        ]);
        $url = $this->sdkUrl('export/csv/sync');
        $res = Query::posturl($url, $data);
        $this->assertOk($res, $url);
        return $res['data'];
    }

    /**
     * @param array<string,mixed>|null $res
     */
    private function assertOk($res, string $url): void
    {
        if (!$res || !is_array($res)) {
            throw new Exception('service_export 无响应: ' . $url);
        }
        if ((int) ($res['code'] ?? 1) !== 0) {
            throw new Exception((string) ($res['message'] ?? 'service_export 返回错误') . ' url=' . $url);
        }
    }
}
