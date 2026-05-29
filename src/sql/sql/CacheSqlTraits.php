<?php
namespace xjryanse\servicesdk\sql\sql;

/**
 * service_sql 宽表 CDC 依赖接口 SDK（迭代 3）。
 *
 * 调用 service_sql POST sql/cache/affectedSqlKeys。
 */
trait CacheSqlTraits
{
    /**
     * 按物理源表查询需刷新的 sqlKey 列表。
     *
     * @param string     $triggerTable  物理表名，与 dbin / w_sql_table 一致
     * @param int|string $onlyCache     1=仅 is_cache=1
     * @param int|string $withTopoOrder 1=子 sql_key 拓扑序
     *
     * @return array<string,mixed> 含 sqlKeys、directSqlKeys、count 等
     */
    public function affectedSqlKeys(string $triggerTable, $onlyCache = 1, $withTopoOrder = 1): array
    {
        $baseUrl = 'sql/cache/affectedSqlKeys';
        $data = $this->postBaseData();
        $data['triggerTable'] = $triggerTable;
        $data['onlyCache'] = $onlyCache;
        $data['withTopoOrder'] = $withTopoOrder;

        $res = $this->queryLog($baseUrl, $data, 'curl');
        if (!$res || !isset($res['data']) || !is_array($res['data'])) {
            return [];
        }

        return $res['data'];
    }
}
