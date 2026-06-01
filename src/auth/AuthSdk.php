<?php
namespace xjryanse\servicesdk\auth;

use xjryanse\servicesdk\comm\SdkBase;
/**
 * 认证服务 SDK
 */
class AuthSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_auth';

    
    /**
     * 后台菜单
     * @param type $msgId   消息id
     * @param type $type    消息类型
     * @param type $param   参数
     */
    public function adminMenu($userId){
        $baseUrl = 'auth/admin/menu';
        // 默认发本地消息中间件
        // TODO:配置解耦
        $data = $this->postBaseData();
        $data['user_id'] = $userId;

        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }

    /**
     * 会话登录：账号密码。
     *
     * @param string $username
     * @param string $password 明文密码
     * @param array $extra 额外参数（如 need_token/passwordBase64/login_ip/domain_name）
     * @return array
     */
    public function sessionLogin($username, $password = '', array $extra = []){
        $baseUrl = 'auth/session/login';
        $data = $this->postBaseData();
        $data['username'] = $username;
        if ($password !== '') {
            $data['password'] = $password;
        }
        if ($extra) {
            $data = array_merge($data, $extra);
        }

        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }

    /**
     * 当前会话信息。
     *
     * @param string $accessToken
     * @param array $extra 额外参数（如 token/jwtToken）
     * @return array
     */
    public function sessionCurrent($accessToken = '', array $extra = []){
        $baseUrl = 'auth/session/current';
        $data = $this->postBaseData();
        if ($accessToken !== '') {
            $data['access_token'] = $accessToken;
        }
        if ($extra) {
            $data = array_merge($data, $extra);
        }

        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }

    /**
     * 会话续期。
     *
     * @param string $accessToken
     * @param bool $rotate true:轮换新 token；false:原 token 延长过期
     * @param array $extra
     * @return array
     */
    public function sessionRefresh($accessToken = '', $rotate = false, array $extra = []){
        $baseUrl = 'auth/session/refresh';
        $data = $this->postBaseData();
        if ($accessToken !== '') {
            $data['access_token'] = $accessToken;
        }
        $data['rotate'] = $rotate ? 1 : 0;
        if ($extra) {
            $data = array_merge($data, $extra);
        }

        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }

    /**
     * 会话登出。
     *
     * @param string $accessToken
     * @param array $extra
     * @return array
     */
    public function sessionLogout($accessToken = '', array $extra = []){
        $baseUrl = 'auth/session/logout';
        $data = $this->postBaseData();
        if ($accessToken !== '') {
            $data['access_token'] = $accessToken;
        }
        if ($extra) {
            $data = array_merge($data, $extra);
        }

        $res = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
}
