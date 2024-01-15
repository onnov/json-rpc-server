<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Traits\JsonHelperTrait;

/**
 * Class RpcProcedureAbstract
 *
 * @package Onnov\JsonRpcServer
 */
abstract class RpcProcedureAbstract implements RpcProcedureInterface
{
    use JsonHelperTrait;

    /**
     * @var RpcRequest
     */
    protected $rpcRequest;

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
