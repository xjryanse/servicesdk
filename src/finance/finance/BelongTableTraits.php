<?php
namespace xjryanse\servicesdk\finance\finance;

use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Arrays;
/**
 * 数据类
 */
trait BelongTableTraits{
    /**
     * 
     * @param type $belongTable
     * @return type
     */
    public function belongTableMatchTpls($belongTable){
        $cacheKey = $this->generateCacheKey(__FUNCTION__, $belongTable);
        $res = SCache::funcGet($cacheKey, function () use ($belongTable){
            $url                    = static::sdkUrl('finance/belong_table/matchTpls');
            $data['belong_table']   = $belongTable;
            $data['svBindId']       = $this->uuid;
            $resp                   = QLogSdk::postAndLog($url, $data);
            $data                   = $resp['data'];
            return $data;
        });
        if(!$res){
            SCache::rm($cacheKey);
        }        
        return $res;
    }
    
    /*
     * 账单明细data匹配
     * 获得写入账单明细用的如下字段：
     *  prize_key
     *  dept_id
     *  customer_id
     *  user_id
     *  statement_name
     * 
     */
    public function statementOrderDataMatch($tableName, $info){
        if(!$tableName || !$info){
            return false;
        }
        $matchTpls  = $this->belongTableMatchTpls($tableName);
        // 注意先后顺序，避免未结账单被后续方法清理
        
        $dataArr = [];
        foreach($matchTpls as $v){
            $prizeKey       = Arrays::value($v, 'prize_key');
            $prizeField     = Arrays::value($v, 'prize_field');

            $data                       = [];
            $data['belong_table']       = $tableName;
            $data['belong_table_id']    = $info['id'];
            // $data['order_id']       = Arrays::value($info, 'order_id');
            $data['prize_key']      = $prizeKey;
            $data['prize']          = Arrays::value($info, $prizeField) ? : 0;;
            $data['dept_id']        = Arrays::value($info, 'dept_id');
            $data['customer_id']    = Arrays::value($v, 'customer_field') ? Arrays::value($info, $v['customer_field']): '' ;
            $data['user_id']        = Arrays::value($v, 'user_field') ? Arrays::value($info, $v['user_field']): '' ;
            $data['statement_name'] = '账单';
            // 生成账单明细:差额补偿
            $dataArr[] = $data;
            // TODO:
            // FinanceStatementOrder::belongTableStatementSaveRam($belongTable, $belongTableId, $prize, $data);
        }
        return $dataArr;
    }
    
}
