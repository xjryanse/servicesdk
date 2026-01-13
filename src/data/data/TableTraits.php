<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\speedy\logic\Url;
use xjryanse\servicesdk\msgq\QLogSdk;
use Exception;

/**
 * 缓存类
 */
trait TableTraits{
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function tableDataGet($tableName,$id){
        $url = static::sdkUrl('data/table/get');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['id']         = $id;

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }

    /**
     * 取单挑数据
     * @param type $tableName   消息id
     * @param type $param       消息类型
     * @param type $whereFields 参数
     */
    public static function tableDataFind($tableName, $param, $whereFields = []){
        $url = static::sdkUrl('data/table/find');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['table_data'] = $param;
        // ['equal']=>['username','id']
        $data['whereFields']= $whereFields;

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }

    /**
     * 
     * @param type $tableName
     * @return type
     */
    public static function tableDataPaginate($tableName, $orderBy='', $param=[]){
        $url = static::sdkUrl('data/table/paginate');
        
        $getP                   = [];
        $getP['table_name']     = $tableName;
        if($orderBy){
            $getP['orderBy']        = $orderBy;
        }
        $urlFinal = Url::addParam($url, $getP);

        $res                    = QLogSdk::postAndLog($urlFinal, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }
    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    public static function tableDataList($tableName, $orderBy='', $param=[], $allowFields= ''){
        $url = static::sdkUrl('data/table/list');
        
        $postP                   = [];
        $postP['table_name']     = $tableName;
        // 逗号分割
        $postP['allowFields']    = $allowFields;
        if($orderBy){
            $postP['orderBy']        = $orderBy;
        }
        $postP['table_data']     = $param;

        $res                    = QLogSdk::postAndLog($url, $postP);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }    
    
    /**
     * 数据表明细统计
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function tableDataDtlCount($tableName,$id){
        $url = static::sdkUrl('data/table/dtlCount');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['field']      = 'inventory_id';
        $data['fieldIds']   = $id;

        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
    
    /**
     * 20260106插入新增数据
     * @param type $tableName
     * @return type
     */
    public static function tableDataInsert($tableName, $data){
        $url = static::sdkUrl('data/table/insert');
        $param['table_name'] = $tableName;
        $param['table_data'] = $data;
        $res                    = QLogSdk::postAndLog($url, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }

    /**
     * 20260106插入更新数据
     * @param type $tableName
     * @return type
     */
    public static function tableDataUpdate($tableName, $data){
        $url                    = static::sdkUrl('data/table/update');
        $param['table_name']     = $tableName;
        $param['table_data']     = $data;
        $res                    = QLogSdk::postAndLog($url, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }    
    
    /**
     * 20260107:插入删除数据
     * @param type $tableName
     * @return type
     */
    public static function tableDataDelete($tableName, $id){
        $url                        = static::sdkUrl('data/table/delete');
        $param['table_name']        = $tableName;
        $param['table_data']['id']  = $id;
        $res                        = QLogSdk::postAndLog($url, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }    
    
}
