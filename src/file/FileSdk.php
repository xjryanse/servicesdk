<?php
namespace xjryanse\servicesdk\file;

use xjryanse\servicesdk\comm\SdkBase;
use xjryanse\servicesdk\comm\SdkTrace;
use xjryanse\servicesdk\exception\SdkCallException;
use xjryanse\phplite\curl\Query;
use Exception;
/**
 * 17点20分
 * 还是改bindId为实例id,dbId另外属性设置
 * 
 */
class FileSdk extends SdkBase{

    // 2026年1月22日用数据库id，作为实例id
    // 需定义：配套BindSdkTrait使用
    protected static $serverKey = 'service_file';

    /**
     * 20260705:本地图片上传（multipart 直传 service_file）
     * - 对应下游：POST /file/picture/upload
     * - 网关/控制器只需拿到本地临时文件 $filePath，一行调用即可完成入库并拿到可访问 URL
     * - 复用 SDK 的 sdkUrl（多机 IP 转换、hostCovMap 已内置）与 SdkTrace（链路可观测性）
     *
     * @param string $filePath 本地临时文件绝对路径（$_FILES['file']['tmp_name']）
     * @param string $fileName 原始文件名（用于后缀校验；缺省时取 basename($filePath)）
     * @param string $folder   保存目录，默认 images（如 avatar / goods 等）
     * @param mixed  $expireTime 过期时间，透传给 SystemFile::uplSave
     * @return array 下游 data 段：{ id, rawPath, file_path, url }
     * @throws Exception
     */
    public function uploadPicture(string $filePath, string $fileName = '', string $folder = 'images', $expireTime = null){
        if(!$filePath || !is_file($filePath)){
            throw new Exception('上传临时文件不存在：'.$filePath);
        }
        $url        = static::sdkUrl('file/picture/upload');
        $startMTs   = intval(microtime(true) * 1000);

        $baseData           = $this->postBaseData();
        $baseData['folder'] = $folder;
        if($expireTime !== null){
            $baseData['expire_time'] = $expireTime;
        }
        $post = $baseData;
        $post['file'] = curl_file_create($filePath, '', $fileName ?: basename($filePath));

        $res      = Query::post($url, $post);
        $endMTs   = intval(microtime(true) * 1000);

        if(!$res){
            $span = SdkTrace::buildHttpSpan($url, $baseData, null, $startMTs, $endMTs, 'fail', '服务无响应');
            SdkTrace::pushSpan($span);
            throw new SdkCallException($url.'无响应', $span, null, gethostname().'无数据:'.$url);
        }
        if(($res['code'] ?? 1) <> 0){
            SdkTrace::mergeServiceArrIntoGlobal($res);
            $msgStr      = isset($res['message']) ? (string) $res['message'] : '';
            $userMessage = SdkTrace::unwrapUserMessage($msgStr);
            $childTrace  = SdkTrace::extractChildTraceFromResponse($res);
            $span = SdkTrace::buildHttpSpan($url, $baseData, $res, $startMTs, $endMTs, 'fail', $userMessage);
            SdkTrace::pushSpan($span);
            throw new SdkCallException($userMessage, $span, $childTrace, $msgStr);
        }
        SdkTrace::mergeServiceArrIntoGlobal($res);
        $span = SdkTrace::buildHttpSpan($url, $baseData, $res, $startMTs, $endMTs, 'ok', '');
        SdkTrace::pushSpan($span);
        return $res['data'];
    }
    /**
     * 优化成功：20260115
     * 执行校验
     * @param type $url
     * @param type $folder
     */
    public function uploadFromUrl(string $url, $folder='images', $expireTime = null, $sufix = 'png'){
        $baseUrl                = 'file/upload/fromUrl';
        $data                   = $this->postBaseData();
        $data['url']            = $url;
        $data['folder']         = $folder;
        $data['expire_time']    = $expireTime;
        $data['sufix']          = $sufix;

        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];     
    }
    
    /**
     * 获取文件信息
     */
    public function infoIdMap($ids){
        $baseUrl                = 'file/info/idMap';
        $data                   = $this->postBaseData();
        $data['id']             = $ids;

        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];     
    }
    
    /**
     * 获取文件信息
     */
    public function get($id){
        $baseUrl                = 'file/info/get';
        $data                   = $this->postBaseData();
        $data['id']             = $id;

        $res        = $this->queryLog($baseUrl, $data, 'curl');
        return $res['data'];     
    }    
    
}
