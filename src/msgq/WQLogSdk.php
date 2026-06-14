<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\comm\TcpRetry;
use xjryanse\servicesdk\comm\SdkTrace;
use xjryanse\servicesdk\comm\TcpCtx;
use xjryanse\phplite\logic\Arrays;
use xjryanse\servicesdk\exception\SdkCallException;
/**
 * 2026年1月14日：使用workerman调用请求
 * 20251227:20点15分
 */
class WQLogSdk extends SdkBase{
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_msgq';

    /**
     * 调用 Workerman 时透传 TraceId，便于对端 Worker 串联同一链路
     */
    public static function request($host, $port, $url, $param){
        $bizParam = is_array($param) ? $param : [];
        $qParam   = TcpCtx::envelope($url, $bizParam);
        $startMTs   = intval(microtime(true) * 1000);
        $resp       = TcpRetry::syncRequest($host, $port, $qParam);
        $endMTs     = intval(microtime(true) * 1000);

        $urlStr = $host.':'.$port.'/'.$url;
        if(!$resp){
            $span = SdkTrace::buildWorkerSpan($host, $port, $url, $bizParam, null, $startMTs, $endMTs, 'fail', '服务无响应');
            SdkTrace::pushSpan($span);
            throw new SdkCallException($url.'无响应', $span, null, gethostname().'接口无值:'.$urlStr);
        }
        if($resp['code'] <> 0){
            SdkTrace::mergeServiceArrIntoGlobal($resp);
            $msgStr = (string) Arrays::value($resp, 'message');
            $userMessage = SdkTrace::unwrapUserMessage($msgStr);
            $childTrace = SdkTrace::extractChildTraceFromResponse($resp);
            $span = SdkTrace::buildWorkerSpan($host, $port, $url, $bizParam, $resp, $startMTs, $endMTs, 'fail', $userMessage);
            SdkTrace::pushSpan($span);
            throw new SdkCallException($userMessage, $span, $childTrace, $msgStr);
        }

        SdkTrace::mergeServiceArrIntoGlobal($resp);
        $span = SdkTrace::buildWorkerSpan($host, $port, $url, $bizParam, $resp, $startMTs, $endMTs, 'ok', '');
        SdkTrace::pushSpan($span);

        return $resp;
    }

    /**
     * @deprecated 请使用 SdkTrace::pushSpan
     */
    public static function log($calleeHost, $calleePort, $calleeRoute, $param, $response, $startMTs, $endMTs){
        $span = SdkTrace::buildWorkerSpan(
            $calleeHost,
            $calleePort,
            $calleeRoute,
            is_array($param) ? $param : [],
            $response,
            $startMTs,
            $endMTs,
            'ok',
            ''
        );
        SdkTrace::pushSpan($span);
    }

}
