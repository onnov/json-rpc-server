<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 10:45
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

use stdClass;

/**
 * Class RpcErrorDefinition
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcErrorDefinition
{
    use CastableToArray;

    /**
     * Description of what went wrong.
     *
     * @var string|null
     */
    private $description = null;

    /**
     * Unique error code.
     *
     * @var int
     */
    private $code;

    /**
     * Unique error message.
     *
     * @var string
     */
    private $message;

    /**
     * SON-Schema of the additional error data.
     *
     * @var stdClass|null
     */
    private $data = null;

    /**
     * Set the logLevel when using the Psr\Log
     *
     * @var string|null
     */
    private $logLevel;

    /**
     * RpcErrorDefinition constructor.
     * @param mixed[] $data
     */
    public function __construct(array $data = [])
    {
        if (isset($data['code'])) {
            $this->code = $data['code'];
        }
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }

        $this->description = $data['description'] ?? null;
        $this->data = $data['data'] ?? null;
        $this->logLevel = $data['logLevel'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return RpcErrorDefinition
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return RpcErrorDefinition
     */
    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return RpcErrorDefinition
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getData(): ?stdClass
    {
        return $this->data;
    }

    /**
     * @param stdClass|null $data
     * @return RpcErrorDefinition
     */
    public function setData(?stdClass $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogLevel(): ?string
    {
        return $this->logLevel;
    }

    /**
     * @param string|null $logLevel
     * @return RpcErrorDefinition
     */
    public function setLogLevel(?string $logLevel): self
    {
        $this->logLevel = $logLevel;

        return $this;
    }
}
