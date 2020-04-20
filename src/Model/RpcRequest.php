<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

class RpcRequest
{
    /** @var int|string|null */
    private $id;

    /** @var string */
    private $method;

    /** @var mixed[]|null  */
    private $params;

    /**
     * RpcRequest constructor.
     *
     * @param array $validRpc
     */
    public function __construct(array $validRpc)
    {
        $this->id = $validRpc['id'] ?? null;
        $this->method = $validRpc['method'];
        $this->params = $validRpc['params'] ?? null;
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
     * @return mixed[]|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }
}
