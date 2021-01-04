<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Model\RpcResultInterface;

/**
 * Class ApiMethodAbstract
 *
 * @package Onnov\JsonRpcServer
 */
abstract class ApiMethodAbstract implements ApiMethodInterface
{
    /** @var RpcRequest */
    protected $rpcRequest;

    /**
     * @return RpcResultInterface
     */
    abstract public function execute(): RpcResultInterface;

    /**
     * @return mixed[]
     */
    abstract public function requestSchema(): array;

    /**
     * @return mixed[]
     */
    abstract public function responseSchema(): array;

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
