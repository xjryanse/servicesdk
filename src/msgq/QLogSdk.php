<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\comm\OutboundLogUtil;
use xjryanse\phplite\curl\Query;
use xjryanse\phplite\logic\LogBuffer;
use Exception;
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
        // 脚本请求开始
        $startMTs   = intval(microtime(true) * 1000);

        $res                    = Query::posturl($url, $request);

        // 脚本请求结束
        $endMTs     = intval(microtime(true) * 1000);
        if(!$res){
            throw new Exception(gethostname().'无数据:'.$url.'参数:'. json_encode($request,JSON_UNESCAPED_UNICODE));
        }
        //2026年1月22日
        if($res['code']<>0){
            throw new Exception('异常:'.$url.'内容:'.$res['message'].'请求参数:'.json_encode($request,JSON_UNESCAPED_UNICODE));
        }

        // 2026年3月22日：开发
        if(isset($res['$dev']) && isset($res['$dev']['serviceArr']) && $res['$dev']['serviceArr']){
            global $serviceTraceArr;
            $serviceTraceArr = $serviceTraceArr
                    ? array_merge($serviceTraceArr, $res['$dev']['serviceArr'])
                    : $res['$dev']['serviceArr'];
        }
        // 调用记录日志
        static::log($url, $request, $res, $startMTs, $endMTs);
        return $res;
    }

    /**
     * 记录日志：入队后请求结束批量写 Redis，带 TraceId/来源，减轻跨网开销
     */
    public static function log($url, $request, $response, $startMTs, $endMTs){
        global $serviceTraceArr;
        $callerSvc = OutboundLogUtil::callerServiceLabel();
        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? 'http';
        $calleeHost = $parts['host'] ?? '';
        $calleePort = isset($parts['port'])
            ? (string) $parts['port']
            : ($scheme === 'https' ? '443' : '80');
        $calleeRoute = ($parts['path'] ?? '/');
        if (!empty($parts['query'])) {
            $calleeRoute .= '?' . $parts['query'];
        }
        $hostDisplay = $calleeHost !== '' ? ($calleeHost . ':' . $calleePort) : '';

        $msg = [
            'trace_id'          => isset($GLOBALS['trace_id']) ? $GLOBALS['trace_id'] : '',
            'call_seq'          => OutboundLogUtil::nextSeq(),
            'call_direction'    => 'outbound',
            'transport'         => 'http',
            'caller_service'    => $callerSvc,
            'caller_from'       => OutboundLogUtil::callerFrame(),
            'callee_scheme'     => $scheme,
            'callee_host'       => $calleeHost,
            'callee_port'       => $calleePort,
            'callee_route'      => $calleeRoute,
            'env'               => getenv('APP_ENV') !== false && getenv('APP_ENV') !== '' ? getenv('APP_ENV') : 'prod',
            'service_name'      => $callerSvc,
            'micro_diff'        => $endMTs - $startMTs,
            'url'               => $url,
            'queryType'         => 'http',
            'host'              => $hostDisplay,
            'request'           => json_encode($request, JSON_UNESCAPED_UNICODE),
            'response'          => mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 500) . '……',
            'create_time'       => date('Y-m-d H:i:s'),
        ];
        $serviceTraceArr[] = $msg;
        LogBuffer::push($msg);
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
