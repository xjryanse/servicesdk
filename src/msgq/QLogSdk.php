<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\comm\SdkTrace;
use xjryanse\phplite\curl\Query;
use xjryanse\servicesdk\exception\SdkCallException;
/**
 * 请求日志调用sdk
 * 20251227:20点15分
 */
class QLogSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_msgq';

    /**
     * 接口post请求:并记录日志
     */
    public static function postAndLog($url, $request){
        $startMTs   = intval(microtime(true) * 1000);
        $request    = is_array($request) ? $request : [];
        $res        = Query::posturl($url, $request);
        $endMTs     = intval(microtime(true) * 1000);

        if(!$res){
            $span = SdkTrace::buildHttpSpan($url, $request, null, $startMTs, $endMTs, 'fail', '服务无响应');
            SdkTrace::pushSpan($span);
            throw new SdkCallException($url.'无响应', $span, null, gethostname().'无数据:'.$url);
        }
        if($res['code']<>0){
            SdkTrace::mergeServiceArrIntoGlobal($res);
            $msgStr = isset($res['message']) ? (string) $res['message'] : '';
            $userMessage = SdkTrace::unwrapUserMessage($msgStr);
            $childTrace = SdkTrace::extractChildTraceFromResponse($res);
            $span = SdkTrace::buildHttpSpan($url, $request, $res, $startMTs, $endMTs, 'fail', $userMessage);
            SdkTrace::pushSpan($span);
            throw new SdkCallException($userMessage, $span, $childTrace, $msgStr);
        }

        SdkTrace::mergeServiceArrIntoGlobal($res);
        $span = SdkTrace::buildHttpSpan($url, $request, $res, $startMTs, $endMTs, 'ok', '');
        SdkTrace::pushSpan($span);
        return $res;
    }

    /**
     * @deprecated 请使用 SdkTrace::pushSpan
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){
        $span = SdkTrace::buildHttpSpan(
            $url,
            is_array($request) ? $request : [],
            $response,
            $startMTs,
            $endMTs,
            'ok',
            ''
        );
        SdkTrace::pushSpan($span);
    }

    /**
     * 执行日志回调上报
     */
    public static function callBack($msgId){
        $url            = 'http://'.static::sdkIp().':9907/msgq/q_log_msg/callback';
        $data['msgId']  = $msgId;

        $res            = Query::posturl($url, $data);

        $resp = [];
        $resp['url']        = $url;
        $resp['request']    = $data;
        $resp['response']   = $res;

        return $resp;
    }
}
