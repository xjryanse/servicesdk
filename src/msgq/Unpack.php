<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\msgq\MsgqSdk;
use xjryanse\phplite\logic\Arrays;

/**
 * 日期
 * 消息的拆包
 */
class Unpack{
    
    public static function unpack($param,$func){
        $msgId      = Arrays::value($param, 'msgId');
        $data       = Arrays::value($param, 'data');
        $qData      = json_decode($data,JSON_UNESCAPED_UNICODE);
        $func($qData);
        global $svBindId;
        MsgqSdk::inst($svBindId)->msgqCallBack($msgId);
        return true;
    }
}
