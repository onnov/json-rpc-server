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
     * @return stdClass|null
     */
    abstract public function requestSchema(): ?stdClass;

    /**
     * @return object|null
     */
    abstract public function customParamsObject(): ?object;

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
