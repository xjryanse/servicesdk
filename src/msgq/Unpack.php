<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\msgq\MsgqSdk;
use xjryanse\phplite\logic\Arrays;

/**
 * 日期
 * 消息的拆包
 */
class Unpack{
    
    public static function unpack($param,$func, $debug = false){
        $msgId      = Arrays::value($param, 'msgId');
        $data       = Arrays::value($param, 'data');
        $qData      = json_decode($data,JSON_UNESCAPED_UNICODE);
        $res = $func($qData);
        if($debug){
            return $res;
        }
        // 非调试模式下，标记消息已消费
        global $svBindId;
        $resp = MsgqSdk::inst($svBindId)->msgqCallBack($msgId);
        return $resp;
    }
}
