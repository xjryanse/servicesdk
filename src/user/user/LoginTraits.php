<?php
namespace xjryanse\servicesdk\user\user;

use xjryanse\phplite\cache\PCache;

/**
 * 身份证相关逻辑
 */
trait LoginTraits{
        
    /**
     * 取单挑数据
     * @param type $userId      用户
     * @param type $ip          ip
     * @param type $domainName  域名
     * @return type
     */
    public function loginLog($userId, $ip='', $domainName=''){
        $baseUrl    = 'user/login/log';
        // 默认发本地消息中间件
        $data = $this->postBaseData();
        $data['user_id']         = $userId;
        $data['login_ip']        = $ip;
        $data['domain_name']     = $domainName;
        
        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
    
        /**
     * 取单挑数据
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function login($username, $password){
        $url = static::sdkUrl('user/session/login');
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data['username']       = $username;
        // service_user/session/login 约定 password 字段为 Base64（与前端直调一致）
        $data['password']       = base64_encode((string) $password);
        $data['svBindId']       = $this->uuid;
        $res                    = QLogSdk::postAndLog($url, $data);
        return $res['data'];
    }
    
}
