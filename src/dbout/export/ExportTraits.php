<?php

namespace xjryanse\servicesdk\dbout\export;

use xjryanse\phplite\curl\Query;
use Exception;

/**
 * service_dbout 导出接口
 */
trait ExportTraits
{
    /**
     * POST dbout/export/paginate
     *
     * @param array<string,mixed> $payload table_name、dbId、page、per_page、orderBy、condition 等
     * @return array<string,mixed>
     */
    public function exportPaginate(array $payload): array
    {
        $data = array_merge($this->postBaseData(), $payload);
        $url = $this->sdkUrl('dbout/export/paginate');
        $res = $this->postBackupJson($url, $data);
        $out = $res['data'] ?? null;
        return is_array($out) ? $out : [];
    }

    /**
     * POST dbout/export/paginateOnlyId
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function exportPaginateOnlyId(array $payload): array
    {
        $data = array_merge($this->postBaseData(), $payload);
        $url = $this->sdkUrl('dbout/export/paginateOnlyId');
        $res = $this->postBackupJson($url, $data);
        $out = $res['data'] ?? null;
        return is_array($out) ? $out : [];
    }

    /**
     * POST dbout/export/count
     *
     * @param array<string,mixed> $payload table_name、dbId、orderBy、condition 等
     * @return array<string,mixed>
     */
    public function exportCount(array $payload): array
    {
        $data = array_merge($this->postBaseData(), $payload);
        $url = $this->sdkUrl('dbout/export/count');
        $res = $this->postBackupJson($url, $data);
        $out = $res['data'] ?? null;
        return is_array($out) ? $out : [];
    }

    /**
     * POST dbout/export/byIds
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function exportByIds(array $payload): array
    {
        $data = array_merge($this->postBaseData(), $payload);
        $url = $this->sdkUrl('dbout/export/byIds');
        $res = $this->postBackupJson($url, $data);
        $out = $res['data'] ?? null;
        return is_array($out) ? $out : [];
    }

    /**
     * POST dbout/export/createTableSql
     * 从源库读取 SHOW CREATE TABLE（只读，供备份库自动建表使用）
     *
     * @return array{create_sql:string,table_name?:string,dbId?:string}
     */
    public function exportCreateTableSql(string $dbId, string $tableName): array
    {
        $data = array_merge($this->postBaseData(), [
            'dbId'       => $dbId,
            'table_name' => $tableName,
        ]);
        $url = $this->sdkUrl('dbout/export/createTableSql');
        $res = $this->postBackupJson($url, $data);
        $out = $res['data'] ?? null;
        return is_array($out) ? $out : [];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function postBackupJson(string $url, array $data): array
    {
        $headers = [];
        $token = property_exists($this, 'backupToken') ? (string) $this->backupToken : '';
        if ($token !== '') {
            $headers['X-Backup-Token'] = $token;
        }
        $res = Query::posturl($url, $data, $headers);
        if (!$res || !is_array($res)) {
            throw new Exception(gethostname() . ' 请求 service_dbout 无响应: ' . $url);
        }
        if ((int) ($res['code'] ?? 1) !== 0) {
            throw new Exception(
                'service_dbout 返回错误: ' . (string) ($res['message'] ?? 'unknown')
                . ' url=' . $url
            );
        }
        return $res;
    }
}
