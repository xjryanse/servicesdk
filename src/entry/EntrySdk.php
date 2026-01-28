<?php
namespace xjryanse\servicesdk\entry;

use xjryanse\phplite\curl\Query;
// use xjryanse\servicesdk\msgq\QLogSdk;
use xjryanse\phplite\logic\Arrays;
use xjryanse\phplite\cache\SCache;
use Exception;
/**
 * 2025年12月30日；11点20分
 * 【静态调用】
 */
class EntrySdk {

    use \xjryanse\servicesdk\entry\phpfpm\HostTraits;

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
     * 取单条数据（一般是phpfpm调用）
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function hostBindInfo($host){
        if($host == '127.0.0.1'){
            throw new Exception('不支持的域名'.$host);
        }
        $cacheKey = __METHOD__.$host;
        // SCache::rm($cacheKey);
        return SCache::funcGet($cacheKey, function () use ($host){        
            $url = static::sdkUrl('entry/host/bindInfo');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['host']   = $host;
            $res                    = Query::posturl($url, $data);
            if(!$res){
                throw new Exception('没有获取到接口数据:'.$url.'参数:'. json_encode($data,JSON_UNESCAPED_UNICODE));
            }                    
            return $res['data'];
        });
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

        $cacheKey = __METHOD__.$bindId;
        // SCache::rm($cacheKey);
        return SCache::funcGet($cacheKey, function () use ($bindId){
            $url = static::sdkUrl('entry/host/bindIdInfo');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['bindId']   = $bindId;
            $res              = Query::posturl($url, $data);
            if(!$res){
                throw new Exception('没有获取到接口数据:'.$url.'参数:'. json_encode($data,JSON_UNESCAPED_UNICODE));
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
        $cacheKey = __METHOD__.$key;
        // SCache::rm($cacheKey);
        return SCache::funcGet($cacheKey, function () use ($key){
            $url    = static::sdkUrl('entry/company/keyInfo');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['key']   = $key;
            $res    = Query::posturl($url, $data);
            if(!$res){
                throw new Exception('没有获取到接口数据:'.$url.'参数:'. json_encode($data,JSON_UNESCAPED_UNICODE));
            }            
            return isset($res['data']) ? $res['data'] : null;
        });
    }
    
    
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public static function companyIdInfo($id){
        $cacheKey = __METHOD__.$id;
        // SCache::rm($cacheKey);
        return SCache::funcGet($cacheKey, function () use ($id){
            $url    = static::sdkUrl('entry/company/info');
            // 默认发本地消息中间件
            // TODO:配置解耦
            $data['id']   = $id;
            $res    = Query::posturl($url, $data);
            if(!$res){
                throw new Exception('没有获取到接口数据:'.$url.'参数:'. json_encode($data,JSON_UNESCAPED_UNICODE));
            }            
            return isset($res['data']) ? $res['data'] : null;
        });
    }
    /**
     * 中台key,提取server列表
     * @param type $serverKey:比如db_data
     */
    public static function serverList($bindId, $serverKey):array{
        $info = static::bindIdInfo($bindId);
        $servers = Arrays::value($info, 'servers')?:[];
        return Arrays::value($servers, $serverKey) ?:[];
    }
    
}
