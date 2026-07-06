<?php
namespace xjryanse\servicesdk\sql\sql;

/**
 * 缓存类
 */
trait DataTraits{
    
    /**
     * 2026年7月6日
     * @param string $sqlKey
     * @param array $param
     * @return type
     */
    public function dataPaginate(string $sqlKey,array $param = [], $page=1,$perPage = 50,$orderBy=''){
        $baseUrl            = 'sql/data/paginate';
        $data               = $this->postBaseData();
        $data['sqlKey']     = $sqlKey;
        $data['param']      = $param;
        $data['page']       = $page;
        $data['per_page']   = $perPage;
        $data['orderBy']    = $orderBy;
        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];            
    }

}
