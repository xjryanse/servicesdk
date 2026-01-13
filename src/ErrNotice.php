<?php
namespace xjryanse\servicesdk;

use xjryanse\speedy\curl\Query;
/**
 * 异常消息通知
 */
class ErrNotice {

    public static function notice($e = null){
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=2e43f76f-0abb-4f79-9ce0-8cf93df1020b';
        
        $msg['msgtype']                         = 'text';
        $msg['text']['content']                 = $_SERVER['SERVER_NAME'].':'.$e->getMessage();

        $res = Query::posturl($url, $msg);

        return $res;
    }
    /**
     * 队列堵塞通知
     */
    public static function msgqJamNotice($queueName, $count){
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=2e43f76f-0abb-4f79-9ce0-8cf93df1020b';
        $text = $_SERVER['SERVER_NAME']."队列堵塞:";
        $text.= "\n队列名称：".$queueName;
        $text.= "\n当前数量：".$count;
        
        $msg['msgtype']                         = 'text';
        $msg['text']['content']                 = $text;

        return Query::posturl($url, $msg);
    }
}
