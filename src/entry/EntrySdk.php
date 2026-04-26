<?php
namespace xjryanse\servicesdk\entry;

use xjryanse\servicesdk\comm\TcpRetry;
use xjryanse\phplite\logic\Arrays;
use xjryanse\phplite\cache\SCache;
use xjryanse\phplite\logic\Env;
use Exception;
/**
 * 2025年12月30日；11点20分
 * 【静态调用】
 */
class EntrySdk {

    use \xjryanse\servicesdk\entry\db\DbTraits;
    use \xjryanse\servicesdk\entry\phpfpm\HostTraits;
    use \xjryanse\servicesdk\entry\svBind\SvBindTraits;

    /**
     * todo:198专用
     * @return type
     */
    public static function sdkIp(){
        // return '127.0.0.1';
        // 入口库在哪里就用哪里的服务，这样避免io开销(临时加)
        return config('database.dbEntry.hostname') ? : '127.0.0.1';
    }
    
    protected static function sdkPort(){
        return '9919';
    }

    protected static function sdkUrl($path){
        return 'http://'.static::sdkIp().':'.static::sdkPort().'/'.$path;  
    }
    /**
     * 
     */
    protected static function wQuery($baseUrl , $param ){
        $host       = Env::value('ServiceEntryHost') ? : '127.0.0.1';
        $port       = '19919';

        $qParam['url']   = $baseUrl;
        $qParam['param'] = $param;

        return TcpRetry::syncRequest($host, $port, $qParam);
    }
    
    /**
     * 取单条数据（一般是phpfpm调用）
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function hostBindInfo($host){
        if($host == '127.0.0.1'){
            throw new Exception('不支持的域名'.$host);
        }
        $cacheKey = static::generateCacheKey(__FUNCTION__, $host);
        // SCache::rm($cacheKey);
        $resp = SCache::funcGet($cacheKey, function () use ($host){        
            // $url = static::sdkUrl('entry/host/bindInfo');
            $baseUrl        = 'entry/host/bindInfo';
            $data['host']   = $host;
            $res = static::wQuery($baseUrl, $data);
            // 2026-03-11：防止$res为null或无data字段时报Trying to access array offset on value of type null
            if(!$res || !is_array($res) || !array_key_exists('data', $res)){
                return null;
            }
            return $res['data'];
        });
        if(!$resp){
            SCache::rm($cacheKey);
        }
        return $resp;
    }

    /**
     * 必传，一般是入口服务透传
     * @return type
     * @throws Exception
     */
    public static function bindIdInfo($bindId){
        if(!$bindId){
            throw new Exception('$bindId必须');
        }
        if(!is_numeric($bindId)){
            throw new Exception('不支持的绑定id格式');
        }

        $cacheKey = static::generateCacheKey(__FUNCTION__, $bindId);
        // SCache::rm($cacheKey);
        return SCache::funcGet($cacheKey, function () use ($bindId){
            $baseUrl    = 'entry/host/bindIdInfo';
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['bindId']   = $bindId;
            $res        = static::wQuery($baseUrl, $data);
            if($res['code']<>0){
                throw new Exception('entry:'.$res['message']);
            }
            return $res ? $res['data'] : [];
        });
    }

    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function companyKeyInfo($key){
        $cacheKey = static::generateCacheKey(__FUNCTION__, $key);
        // SCache::rm($cacheKey);
        $res = SCache::funcGet($cacheKey, function () use ($key){
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['key']   = $key;
            
            $baseUrl    = 'entry/company/keyInfo';
            $res        = static::wQuery($baseUrl, $data);

            return isset($res['data']) ? $res['data'] : null;
        });
        if(!$res){
            SCache::rm($cacheKey);
        }
        return $res;
    }
    
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function companyIdInfo($id){
        $cacheKey = static::generateCacheKey(__FUNCTION__, $id);
        // SCache::rm($cacheKey);
        $res = SCache::funcGet($cacheKey, function () use ($id){
            // TODO:配置解耦
            $data['id']     = $id;
            $baseUrl        = 'entry/company/info';
            $res            = static::wQuery($baseUrl, $data);
            return isset($res['data']) ? $res['data'] : null;
        });        
        if(!$res){
            SCache::rm($cacheKey);
        }
        return $res;
    }
    
    /**
     * 2026年3月11日
     * @param type $host
     * @return type
     * @throws Exception
     */
    public static function hostCovMap(){
        $cacheKey = static::generateCacheKey(__FUNCTION__);
        // SCache::rm($cacheKey);
        $res = SCache::funcGet($cacheKey, function (){
            $baseUrl        = 'entry/hostCov/map';
            $res = static::wQuery($baseUrl, []);
            return isset($res['data']) ? $res['data'] : [];
        });
        if(!$res){
            SCache::rm($cacheKey);
        }
        return $res;
    }
    /**
     * 中台key,提取server列表
     * @param type $serverKey:比如db_data
     */
    public static function serverList($bindId, $serverKey):array{
        $info       = static::bindIdInfo($bindId);
        $servers    = Arrays::value($info, 'servers')?:[];
        return Arrays::value($servers, $serverKey) ?:[];
    }
    
    /**
     * 集中管理缓存规则
     * @param string $method
     * @param type $subFix
     * @return string
     */
    protected static function generateCacheKey(string $method, $subFix = null): string {
        $key = __METHOD__.$method . md5(static::sdkIp());
        if ($subFix !== null) {
            $key .= $subFix;
        }
        return $key;
    }
    
    /**
     * 
     * @param type $key
     */
    public static function clearCache($method, $key){
        $cacheKey = static::generateCacheKey($method, $key);
        SCache::rm($cacheKey);
    }
}
