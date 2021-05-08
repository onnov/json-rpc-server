<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 13:33
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

/**
 * Class RpcModel
 * @package Onnov\JsonRpcServer\Model
 */
class RpcModel
{
    /** @var string */
    private $jsonrpc;

    /** @var string */
    private $method;

    /** @var mixed|null */
    private $params;

    /** @var mixed|null */
    private $id;

    /**
     * @return string
     */
    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    /**
     * @param string $jsonrpc
     */
    public function setJsonrpc(string $jsonrpc): void
    {
        $this->jsonrpc = $jsonrpc;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return mixed|null
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed|null $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed|null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
