<?php

namespace xjryanse\servicesdk\exception;

/**
 * 跨服务调用失败：message 仅含业务文案，完整调试信息在 span / childTrace。
 */
class SdkCallException extends \Exception
{
    /** @var string 给用户 / 上层 message 字段 */
    public $userMessage;

    /** @var array<string,mixed> 本次出站 span */
    public $span;

    /** @var array<string,mixed>|null 下游 trace（含 spans / error） */
    public $childTrace;

    /** @var string 原始响应 message（调试） */
    public $rawMessage;

    /**
     * @param string $userMessage
     * @param array<string,mixed> $span
     * @param array<string,mixed>|null $childTrace
     * @param string $rawMessage
     */
    public function __construct($userMessage, array $span = [], $childTrace = null, $rawMessage = '')
    {
        $this->userMessage = (string) $userMessage;
        $this->span = $span;
        $this->childTrace = is_array($childTrace) ? $childTrace : null;
        $this->rawMessage = $rawMessage !== '' ? (string) $rawMessage : $this->userMessage;
        parent::__construct($this->userMessage);
    }
}
