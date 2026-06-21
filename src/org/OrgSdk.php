<?php

namespace xjryanse\servicesdk\org;

use xjryanse\servicesdk\comm\SdkBase;

/**
 * 消息中台 service_message 接入 SDK
 */
class OrgSdk extends SdkBase {

    protected static $serverKey = 'service_org';

    /**
     * 
     * @param type $dataId
     * @param type $msgTplCode
     * @param array $param
     * @param type $msgChannel
     * @param type $channel
     * @return type
     */
    public function userJobs($userId) {
        $baseUrl        = 'org/user/jobList';
        $data           = $this->postBaseData();
        $data['userId'] = $userId;

        $res            = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
}
