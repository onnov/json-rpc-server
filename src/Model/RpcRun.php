<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 19:17
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

use Onnov\JsonRpcServer\RpcFactoryInterface;
use Onnov\JsonRpcServer\Definition\RpcAuthDefinition;

/**
 * Class RpcRun
 * @package Onnov\JsonRpcServer\Model
 */
class RpcRun
{
    /** @var RpcFactoryInterface */
    private $rpcFactory;

    /** @var string */
    private $json;

    /**
     * @var RpcAuthDefinition
     */
    private $auth;

    /** @var bool */
    private $responseCheck;

    /**
     * RunModel constructor.
     * @param mixed[] $data
     */
    public function __construct(array $data = [])
    {
        if (isset($data['rpcFactory'])) {
            $this->rpcFactory = $data['rpcFactory'];
        }
        if (isset($data['json'])) {
            $this->json = $data['json'];
        }

        $this->auth = $data['auth'] ?? new RpcAuthDefinition();
        $this->responseCheck = $data['responseCheck'] ?? false;
    }

    /**
     * @return RpcFactoryInterface
     */
    public function getRpcFactory(): RpcFactoryInterface
    {
        return $this->rpcFactory;
    }

    /**
     * @param RpcFactoryInterface $rpcFactory
     * @return RpcRun
     */
    public function setRpcFactory(RpcFactoryInterface $rpcFactory): self
    {
        $this->rpcFactory = $rpcFactory;

        return $this;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @param string $json
     * @return RpcRun
     */
    public function setJson(string $json): self
    {
        $this->json = $json;

        return $this;
    }

    /**
     * @return RpcAuthDefinition
     */
    public function getAuth(): RpcAuthDefinition
    {
        return $this->auth;
    }

    /**
     * @param RpcAuthDefinition $auth
     * @return RpcRun
     */
    public function setAuth(RpcAuthDefinition $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResponseCheck(): bool
    {
        return $this->responseCheck;
    }

    /**
     * @param bool $responseCheck
     * @return RpcRun
     */
    public function setResponseCheck(bool $responseCheck): self
    {
        $this->responseCheck = $responseCheck;

        return $this;
    }
}
