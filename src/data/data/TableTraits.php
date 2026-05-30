<?php
namespace xjryanse\servicesdk\data\data;

use xjryanse\phplite\logic\Url;
use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\servicesdk\msgq\WQLogSdk;
use xjryanse\phplite\logic\DataCheck;
use Exception;

/**
 * 数据表微服务访问；读路径带「单次请求内」内存去重（非跨请求持久化）。
 */
trait TableTraits{
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function tableDataGet($tableName,$id, $emptyErr = true){
        $baseUrl = 'data/table/get';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['table_name'] = $tableName;
        $data['id']         = $id;
        // 2026年1月21日：新增dbId入参
        $data['dbId']       = $this->dbId;
        $data['svBindId']   = $this->uuid;
        // 当没有取到数据时，是否抛异常？默认抛异常
        $data['emptyErr']   = $emptyErr ? 1 : 0;

        $host = $this->workerIp();
        $port = $this->workerPort();
        return $this->dataSdkWorkermanMemoRead('data/table/get', $data, function () use ($host, $port, $baseUrl, $data) {
            $res = WQLogSdk::request($host, $port, $baseUrl, $data);
            return $res['data'];
        });
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
        return $this->dataSdkWorkermanMemoRead('data/table/find', $data, function () use ($host, $port, $baseUrl, $data) {
            $res = WQLogSdk::request($host, $port, $baseUrl, $data);
            return $res['data'];
        });
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
        return $this->dataSdkWorkermanMemoRead('data/table/conFind', $data, function () use ($host, $port, $baseUrl, $data) {
            $res = WQLogSdk::request($host, $port, $baseUrl, $data);
            return $res['data'];
        });
    }    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    public function tableDataPaginate($tableName, $orderBy='', $con=[], $param=[]){
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
     * 分页仅主键列（SELECT tie 字段），条件/排序/分页参数同 tableDataPaginate。
     *
     * @param string $tableName
     * @param string $orderBy
     * @param array $con
     * @param array $param 可选 tie_field / id_field
     * @return mixed
     */
    public function tableDataPaginateOnlyId($tableName, $orderBy = '', $con = [], $param = [])
    {
        $param['table_name'] = $tableName;
        if ($orderBy) {
            $param['orderBy'] = $orderBy;
        }
        $param['dbId'] = $this->dbId;
        $param['svBindId'] = $this->uuid;
        $param['condition'] = $con;

        $baseUrl = 'data/table/paginateOnlyId';
        $res = $this->queryLog($baseUrl, $param, 'worker');
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
        return $this->dataSdkWorkermanMemoRead('data/table/list', $postP, function () use ($host, $port, $baseUrl, $postP) {
            $res = WQLogSdk::request($host, $port, $baseUrl, $postP);
            if (!$res) {
                throw new Exception('没有获取到接口数据:' . $baseUrl);
            }
            return $res['data'];
        });
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
        return $this->dataSdkWorkermanMemoRead('data/table/conList', $postP, function () use ($host, $port, $baseUrl, $postP, $url) {
            $res = WQLogSdk::request($host, $port, $baseUrl, $postP);
            if (!$res) {
                throw new Exception('没有获取到接口数据:' . $url);
            }
            return $res['data'];
        });
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
        // $url = static::sdkUrl('data/table/insert');
        $baseUrl = 'data/table/insert';
        $param['table_name'] = $tableName;
        $param['table_data'] = $data;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;
        
        $res = $this->queryLog($baseUrl, $param, 'worker');
        return $res['data'];
    }

    /**
     * 20260106插入更新数据
     * @param type $tableName
     * @return type
     */
    public function tableDataUpdate($tableName, $data){
        // $url                    = static::sdkUrl('data/table/update');
        
        $baseUrl = 'data/table/update';        
        $param['table_name']     = $tableName;
        $param['table_data']     = $data;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;        
        
        // $res                    = QLogSdk::postAndLog($url, $param);
        $res = $this->queryLog($baseUrl, $param, 'worker');
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$baseUrl);
        }
        return $res['data'];
    }    
    
    /**
     * 20260107:插入删除数据
     * @param type $tableName
     * @return type
     */
    public function tableDataDelete($tableName, $id){
        if ($id === null || $id === '' || (is_string($id) && trim($id) === '')) {
            throw new Exception('删除操作必须传入有效主键 id（table=' . $tableName . '）');
        }
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
        $baseUrl = 'data/table/fieldArr';
        $param['table_name']     = $tableName;
        // 2026年1月21日：新增dbId入参
        $param['dbId']       = $this->dbId;
        $param['svBindId']   = $this->uuid;        

        return $this->dataSdkWorkermanMemoRead('data/table/fieldArr', $param, function () use ($baseUrl, $param) {
            $res = $this->queryLog($baseUrl, $param, 'worker');
            return $res['data'];
        });
    }

    /**
     * 单次请求生命周期内：相同参数只调一次微服务，结果放在 $GLOBALS，请求结束随 PHP 回收销毁。
     * FPM/mod_php 默认开启；CLI/Workerman 长进程默认关闭（避免多任务共用 $GLOBALS 串数据），需要时在单条消息入口清空后再设 DATA_SDK_REQ_MEMO=1。
     * 关闭：DATA_SDK_REQ_MEMO=0
     *
     * @param string $op 区分操作，与 $data 一起参与键
     * @param array $data 实际发往微服务的参数（含 dbId、svBindId）
     */
    private function dataSdkWorkermanMemoRead(string $op, array $data, callable $fetch) {
        $forceOff = getenv('DATA_SDK_REQ_MEMO') === '0';
        $forceOn = getenv('DATA_SDK_REQ_MEMO') === '1';
        if ($forceOff || (!$forceOn && PHP_SAPI === 'cli')) {
            return $fetch();
        }
        $key = $op . "\0" . md5(json_encode($data, JSON_UNESCAPED_UNICODE));
        if (!isset($GLOBALS['__data_sdk_wm_memo']) || !is_array($GLOBALS['__data_sdk_wm_memo'])) {
            $GLOBALS['__data_sdk_wm_memo'] = [];
        }
        $memo = &$GLOBALS['__data_sdk_wm_memo'];
        if (array_key_exists($key, $memo)) {
            return $memo[$key];
        }
        $memo[$key] = $fetch();
        return $memo[$key];
    }
    
}
