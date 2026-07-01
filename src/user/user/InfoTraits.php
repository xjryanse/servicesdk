<?php
namespace xjryanse\servicesdk\user\user;

/**
 * 身份证相关逻辑
 */
trait InfoTraits{
    /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function batchGet($userIds){
        $baseUrl    = 'user/user/batchGet';
        // 默认发本地消息中间件
        $data = $this->postBaseData();
        $data['id']         = $userIds;
        
        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
    /**
     * 单条（也走批量）
     * @param type $userId
     */
    public function get($userId){
        $lists = $this->batchGet([$userId]);
        return $lists ? $lists[0] : []; 
    }
    /**
     * service_dev
     * 手机号码获取用户信息，无用户时会创建一个
     * @return type
     */
    public function phoneUserInfo($phone, $realname =''){
        $baseUrl    = 'user/user/phoneUserInfo';
        // 默认发本地消息中间件
        $data = $this->postBaseData();
        $data['phone']         = $phone;
        if($realname){
            $data['realname']      = $realname;
        }
        
        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
}
