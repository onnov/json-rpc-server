<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Model\RpcResultInterface;
use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use stdClass;

/**
 * Class ApiMethodAbstract
 *
 * @package Onnov\JsonRpcServer
 */
abstract class ApiMethodAbstract implements ApiMethodInterface
{
    use JsonHelperTrait;

    /** @var RpcRequest */
    protected $rpcRequest;

    /**
     * @return RpcResultInterface
     */
    abstract public function execute(): RpcResultInterface;

    /**
     * custom php object for mapping request params
     * or null if not required
     *
     * @return object|null
     */
    abstract public function customParamsObject(): ?object;

    /**
     * @return stdClass|null
     */
    abstract public function requestSchema(): ?stdClass;

    /**
     * @return stdClass|null
     */
    abstract public function responseSchema(): ?stdClass;

    /**
     * @return RpcRequest
     */
    public function getRpcRequest(): RpcRequest
    {
        return $this->rpcRequest;
    }

    /**
     * @param RpcRequest $rpcRequest
     *
     * @return self
     */
    public function setRpcRequest(RpcRequest $rpcRequest): self
    {
        $this->rpcRequest = $rpcRequest;

        return $this;
    }
}
