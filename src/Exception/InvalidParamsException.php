<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 24.06.19
 * Time: 17:55
 */

namespace Onnov\JsonRpcServer\Exception;

use RuntimeException;
use Throwable;

/**
 * Class InvalidParamsException
 *
 * @package Onnov\JsonRpcServer\Exception
 */
class InvalidParamsException extends RuntimeException
{
    /** @var mixed[]|null */
    protected $data;

    /**
     * InvalidParamsException constructor.
     * @param string $message
     * @param mixed[]|null $data
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = "",
        ?array $data = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed[]|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
