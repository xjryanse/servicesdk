<?php
namespace xjryanse\servicesdk\user\user;

use xjryanse\phplite\cache\PCache;

/**
 * 身份证相关逻辑
 */
trait IdNoTraits{
    /**
     * 2026年1月23日
     * @param type $pageKey
     * @return type
     */
    public function idNoParseUpsetSexBirthday($userId, $idno){
        // TODO:配置解耦
        $data = $this->postBaseData();            
        $data['userId']     = $userId;
        $data['idno']       = $idno;

        $baseUrl    = 'user/idno/parseUpsetSexBirthday';
        $res        = $this->queryLog($baseUrl, $data, 'worker');
        // $res                = QLogSdk::postAndLog($url, $data);
        return $res['data'];        
        
    }
}
