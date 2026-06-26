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
     * 同步导出 excel
     *
     * @param array<int, array<string, mixed>> $exportData
     * @param array<int, array<string, mixed>> $columns
     */
    public function excelSync(array $exportData, array $columns) {
        $param['exportData'] = $exportData;
        $param['columns']    = $columns;
        
        $data = array_merge($this->postBaseData(), $param);
        $baseUrl    = 'export/excel/sync';
        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }
    
    /* 下方接口待验证 **********************/



    /**
     * 已转换数据 → 文件（自动选 Excel/CSV）
     *
     * @param array<string,mixed> $param
     * @return array<string,mixed>
     */
    public function tableSync(array $param)
    {
        $data = array_merge($this->postBaseData(), $param);
        $baseUrl    = 'export/table/sync';
        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];
    }

    /**
     * 同步导出 csv
     *
     * @param array<int, array<string, mixed>> $exportData
     * @param array<int, array<string, mixed>> $columns
     */
    public function csvSync(array $exportData, array $columns)
    {
        $data = array_merge($this->postBaseData(), [
            'exportData' => $exportData,
            'columns'    => $columns,
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
