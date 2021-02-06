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
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
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
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
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
     */
    public function setData(?stdClass $data): void
    {
        $this->data = $data;
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
     */
    public function setLogLevel(?string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }
}
