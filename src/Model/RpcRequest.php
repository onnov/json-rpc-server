<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

use stdClass;

class RpcRequest
{
    /**
     * @var int|string|null 
     */
    private $id;

    /**
     * @var string 
     */
    private $method;

    /**
     * @var mixed[]|stdClass|null 
     */
    private $params;

    /**
     * parameters in a custom php object
     *
     * @var object|null
     */
    private $paramsObject;

    /**
     * RpcRequest constructor.
     *
     * @param RpcModel    $validRpc
     * @param object|null $paramsObject
     */
    public function __construct(RpcModel $validRpc, ?object $paramsObject = null)
    {
        $this->id = $validRpc->getId();
        $this->method = $validRpc->getMethod();
        $this->params = $validRpc->getParams();
        $this->paramsObject = $paramsObject;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return mixed[]|stdClass|null
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return object|null
     */
    public function getParamsObject(): ?object
    {
        return $this->paramsObject;
    }
}
