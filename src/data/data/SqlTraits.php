<?php
namespace xjryanse\servicesdk\data\data;

use speedy\logic\Url;
use xjryanse\servicesdk\msgq\QLogSdk;
use Exception;

/**
 * 缓存类
 */
trait SqlTraits{
    /**
     * 20251227
     * @param type $sqlKey
     * @param type $orderBy
     * @param type $param
     * @return type
     * @throws Exception
     */
    public static function sqlDataPaginate($sqlKey, $orderBy='', $param=[]){
        $url = static::sdkUrl('data/sql/paginate');
        
        $getP                   = [];
        $getP['sqlKey']         = $sqlKey;
        if($orderBy){
            $getP['orderBy']    = $orderBy;
        }
        $urlFinal               = Url::addParam($url, $getP);

        $res                    = QLogSdk::postAndLog($urlFinal, $param);
        if(!$res){
            throw new Exception('没有获取到接口数据:'.$url);
        }
        return $res['data'];
    }

}
