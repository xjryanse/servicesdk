<?php
namespace xjryanse\servicesdk\export;

use xjryanse\phplite\logic\Arrays;
use xjryanse\servicesdk\DbSdk;
use xjryanse\servicesdk\data\DataSdk;
use xjryanse\servicesdk\universal\UniversalSdk;
use xjryanse\servicesdk\export\ExportSdk;
use xjryanse\servicesdk\universal\UniversalSdk;

/**
 * 万能表按钮导出编排（UniversalSdk + DataSdk + ExportSdk）
 */
final class UniversalTableExport
{
    /**
     * 从 uniBtnId 关联的 table 导出 Excel。
     */
    public static function exportFromBtn(array $rows, $sumData, $uniBtnId, $svBindId, $domain ): array
    {
        // 步骤1：动态数据提取
        $dynArrs    = UniversalSdk::inst($svBindId)->btnTableDynArrs($uniBtnId);
        $dynDataList = static::recordDynDataList($rows, $dynArrs, $svBindId);
        // 步骤2：数据组装
        foreach($rows as &$v){
            foreach($dynDataList as $k1=>$v1){
                if(!isset($v[$k1])){
                    continue;
                }
                $v[$k1] = Arrays::value($v1, $v[$k1]) ?: $v[$k1];
            }
        }
        // 步骤3：表头提取
        $fields     = UniversalSdk::inst($svBindId)->exportFields($p);        
        $columns = $fields['columns'];
        // 步骤4：导出
        $r = ExportSdk::inst($svBindId)->excelSync($rows, $columns);

        $fileName = Arrays::value($r, 'fileName');
        
        $resp = [];
        $resp['fileName']   = $fileName;
        $resp['url']        = $domain.'/files/'.$fileName;
        return $resp;        
    }
    
    protected static function recordDynDataList(array $dataArr, array $dynArrs, $svBindId = ''): array {
        if (!$dynArrs) {
            return [];
        }
        $dbId = DbSdk::dbId('dbBusi', $svBindId);
        $list = DataSdk::inst($svBindId)
            ->dbBind($dbId)
            ->dynDataList($dataArr, $dynArrs);

        return is_array($list) ? $list : [];
    }

}
