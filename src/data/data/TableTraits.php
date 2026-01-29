<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\phplite\logic\Url;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use xjryanse\phplite\logic\DataCheck;
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
    public function tableDataGet($tableName,$id){
        $baseUrl = 'data/table/get';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['id']         = $id;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $data);        
        return $res['data'];
    }
    /**
     * 取单挑数据
     * @param type $tableName   消息id
     * @param type $param       消息类型
     * @param type $whereFields 参数
     */
    public function tableDataFind($tableName, $param, $whereFields = []){
        // $url = static::sdkUrl('data/table/find');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name']     = $tableName;
        $data['table_data']     = $param;
        // ['equal']=>['username','id']
        $data['whereFields']    = $whereFields;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;

        $baseUrl = 'data/table/find';
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $data);   

        return $res['data'];
    }
    /**
     * 2026年1月19日
     * @param type $tableName
     * @param type $con
     * @param type $orderBy
     * @param string $allowFields
     * @return type
     */
    public function tableDataConFind($tableName, $con=[], $orderBy='', string $allowFields= ''){
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['condition']  = $con;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        
        $baseUrl = 'data/table/find';
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $data);        
        return $res['data'];
    }    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    public function tableDataPaginate($tableName, $orderBy='', $con=[]){
        $param['table_name']     = $tableName;
        if($orderBy){
            $param['orderBy']    = $orderBy;
        }
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;
        $param['condition']  = $con;

        $baseUrl    = 'data/table/paginate';
        $res        = $this->queryLog($baseUrl, $param, 'worker');
        return $res['data'];
    }
    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    public function tableDataList($tableName, $orderBy='', $param=[], string $allowFields= ''){
        $baseUrl = 'data/table/list';
        
        $postP                   = [];
        $postP['table_name']     = $tableName;
        // 逗号分割
        $postP['allowFields']    = $allowFields;
        if($orderBy){
            $postP['orderBy']        = $orderBy;
        }
        $postP['table_data']     = $param;
        // 2026年1月21日：新增dbId入参
        $postP['dbId']       = $this->dbId;
        $postP['svBindId']   = $this->uuid;

        // $res = Sync::request($host, $port, $send_data);
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $postP);
        // $res                    = QLogSdk::postAndLog($url, $postP);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$baseUrl);
        }
        return $res['data'];
    }
    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    public function tableDataConList($tableName, $con=[], $orderBy='', string $allowFields= ''){
        $baseUrl = 'data/table/list';
        
        $url = static::sdkUrl($baseUrl);
        
        $postP                   = [];
        $postP['table_name']     = $tableName;
        // 逗号分割
        $postP['allowFields']    = $allowFields;
        if($orderBy){
            $postP['orderBy']       = $orderBy;
        }
        $postP['condition']         = $con;
        // 2026年1月21日：新增dbId入参
        $postP['dbId']       = $this->dbId;
        $postP['svBindId']   = $this->uuid;
        
        // $res = Sync::request($host, $port, $send_data);
        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $postP);
        // $res                    = QLogSdk::postAndLog($url, $postP);
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
    public function tableDataDtlCount($tableName,$id, $field='inventory_id'){
        $url = static::sdkUrl('data/table/dtlCount');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['field']      = $field;
        $data['fieldIds']   = $id;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
    
    /**
     * 20260106插入新增数据
     * @param type $tableName
     * @return type
     */
    public function tableDataInsert($tableName, $data){
        $url = static::sdkUrl('data/table/insert');
        $param['table_name'] = $tableName;
        $param['table_data'] = $data;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;
        
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
    public function tableDataUpdate($tableName, $data){
        $url                    = static::sdkUrl('data/table/update');
        $param['table_name']     = $tableName;
        $param['table_data']     = $data;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;        
        
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
    public function tableDataDelete($tableName, $id){
        $url                        = static::sdkUrl('data/table/delete');
        $param['table_name']        = $tableName;
        $param['table_data']['id']  = $id;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;        
        
        $res                        = QLogSdk::postAndLog($url, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }

    /**
     * 2026年1月15日：数据保存，有数据新增，没数据更新
     * @param type $tableName
     * @return type
     */
    public function tableDataSave($tableName, array $data){
        // id需要由外部传入
        $keys = ['id'];
        DataCheck::must($data, $keys);
        $baseUrl = 'data/tableW/save';
        
        $postP                   = [];
        $postP['table_name']     = $tableName;
        $postP['table_data']     = $data;
        // 2026年1月21日：新增dbId入参
        $postP['dbId']       = $this->dbId;
        $postP['svBindId']   = $this->uuid;        

        $host = $this->workerIp();
        $port = $this->workerPort();
        $res = WQLogSdk::request($host, $port, $baseUrl, $postP);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$baseUrl);
        }
        return $res['data'];
    }
    
    /**
     * 2026年1月29日获取数据表有的字段
     * @param type $tableName
     * @return type
     */
    public function tableFieldArr($tableName){
        $url                    = static::sdkUrl('data/table/fieldArr');
        $param['table_name']     = $tableName;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;        
        
        $res                    = QLogSdk::postAndLog($url, $param);
        return $res['data'];
    }    
    
}
