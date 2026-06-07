<?php

namespace xjryanse\servicesdk\uniform\uniform;

/**
 * uniform 微服务通用请求
 */
trait RequestTraits
{
    /**
     * @param string $baseUrl 如 uniform/record/get
     * @param array<string,mixed> $param
     * @param string $channel curl|worker
     * @return mixed
     */
    protected function uniformRequest($baseUrl, array $param = [], $channel = 'worker')
    {
        $post = array_merge($this->postBaseData(), $param);
        $resp = $this->queryLog($baseUrl, $post, $channel);
        return $resp['data'];
    }
}
