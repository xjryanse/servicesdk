<?php
namespace xjryanse\servicesdk\msgq;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\comm\TcpRetry;
use xjryanse\servicesdk\comm\OutboundLogUtil;
use xjryanse\phplite\logic\LogBuffer;
use xjryanse\phplite\logic\Arrays;
use Exception;
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
        if (!empty($GLOBALS['trace_id'])) {
            $param = array_merge(is_array($param) ? $param : [], ['X-Trace-Id' => $GLOBALS['trace_id']]);
        }
        $qParam = [];
        $qParam['url']   = $url;
        $qParam['param'] = $param;
        $startMTs   = intval(microtime(true) * 1000);
        $resp       = TcpRetry::syncRequest($host, $port, $qParam);
        $endMTs     = intval(microtime(true) * 1000);

        $urlStr = $host.':'.$port.'/'.$url;
        if(!$resp){
            throw new Exception(gethostname().'接口无值:'.$urlStr.'请求参数'. json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        //2026年1月22日
        if($resp['code'] <> 0){
            $msgStr = Arrays::value($resp, 'message');
            throw new Exception(gethostname().'接口异常:'.$urlStr.'内容:'.$msgStr.'请求参数'. json_encode($param, JSON_UNESCAPED_UNICODE));
        }

        static::log($host, $port, $url, $param, $resp, $startMTs, $endMTs);

        return $resp;
    }

    /**
     * 记录日志：入队后请求结束批量写 Redis，带 TraceId/来源，减轻跨网开销
     *
     * @param string $calleeHost Workerman 目标 IP
     * @param string|int $calleePort 端口
     * @param string $calleeRoute 如 data/table/list
     * @param array $param 业务入参（与帧内 param 一致）
     */
    public static function log($calleeHost, $calleePort, $calleeRoute, $param, $response, $startMTs, $endMTs){
        global $serviceTraceArr;
        $callerSvc = OutboundLogUtil::callerServiceLabel();
        $urlStr = $calleeHost . ':' . $calleePort . '/' . $calleeRoute;
        $msg = [
            'trace_id'          => isset($GLOBALS['trace_id']) ? $GLOBALS['trace_id'] : '',
            'call_seq'          => OutboundLogUtil::nextSeq(),
            'call_direction'    => 'outbound',
            'transport'         => 'workerman',
            'caller_service'    => $callerSvc,
            'caller_from'       => OutboundLogUtil::callerFrame(),
            'callee_host'       => (string) $calleeHost,
            'callee_port'       => (string) $calleePort,
            'callee_route'      => $calleeRoute,
            'env'               => getenv('APP_ENV') !== false && getenv('APP_ENV') !== '' ? getenv('APP_ENV') : 'prod',
            'service_name'      => $callerSvc,
            'url'               => $urlStr,
            'micro_diff'        => $endMTs - $startMTs,
            'queryType'         => 'workerman',
            'host'              => $calleeHost . ':' . $calleePort,
            'sourceHostName'    => gethostname(),
            'request'           => json_encode($param, JSON_UNESCAPED_UNICODE),
            'response'          => mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 500) . '……',
            'create_time'       => date('Y-m-d H:i:s'),
        ];
        $serviceTraceArr[] = $msg;
        LogBuffer::push($msg);
    }

}
