<?php
namespace xjryanse\servicesdk;

use speedy\curl\Query;
/**
 * 异常消息通知
 */
class ErrNotice {

    public static function notice($e = null){
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=2e43f76f-0abb-4f79-9ce0-8cf93df1020b';
        
        $msg['msgtype']                         = 'text';
        $msg['text']['content']                 = '开发测试';

        $res = Query::posturl($url, $msg);

        return $res;
    }
    
}
