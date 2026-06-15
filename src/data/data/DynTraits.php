<?php
namespace xjryanse\servicesdk\data\data;

/**
 * 动态枚举值解析（配置由 UniversalSdk::tableDynArrs 提供）
 */
trait DynTraits {

    /**
     * @param array $dataArr       分页 data 数组
     * @param array $dynArrs       UniversalSdk::tableDynArrs 返回值
     * @param array $extraDynDatas 可选额外 dyn 配置
     */
    public function dynDataList(array $dataArr, array $dynArrs, array $extraDynDatas = []) {
        $param = $this->postBaseData();
        $param['dbId']     = $this->dbId;
        $param['svBindId'] = $this->uuid;
        $param['data']     = $dataArr;
        $param['dynArrs']  = $dynArrs;
        $param['dynDatas'] = $extraDynDatas;

        $baseUrl = 'data/dyn/dynDataList';
        $res = $this->queryLog($baseUrl, $param, 'curl');
        return $res['data'];
    }
}
