<?php

namespace xjryanse\servicesdk\cert\cert;

/**
 * 证件微服务通用 HTTP/Worker 请求
 */
trait RequestTraits
{
    /**
     * @param string               $baseUrl 如 cert/cert/certNo
     * @param array<string,mixed>  $param
     * @param string               $channel curl|worker
     * @return mixed
     */
    protected function certRequest($baseUrl, array $param = [], $channel = 'worker')
    {
        $post = array_merge($this->postBaseData(), $param);
        $resp = $this->queryLog($baseUrl, $post, $channel);
        return $resp['data'];
    }

    /**
     * 只读接口缓存 key（含 svBindId 与参数维度）
     *
     * @param string     $method
     * @param mixed|null $suffix
     */
    protected function certCacheKey(string $method, $suffix = null): string
    {
        $key = static::class . $method . $this->uuid;
        if ($suffix !== null) {
            $key .= is_scalar($suffix) ? (string) $suffix : md5(json_encode($suffix));
        }
        return $key;
    }
}
