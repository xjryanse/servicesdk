<?php
namespace xjryanse\servicesdk\goods;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 
 */
class GoodsSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_goods';

    /**
     * 获取商品
     * @param type $goodsId
     * @return type
     */
    public function goodsInfo($goodsId) {
        $baseUrl    = 'goods/goods/info';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $qParam       = $this->postBaseData();
        $qParam['id'] = $goodsId;

        $res        = $this->queryLog($baseUrl, $qParam, 'worker');
        return $res['data'];
    }


}
