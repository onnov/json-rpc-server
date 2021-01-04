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
class MethodErrorException extends RuntimeException
{
    /** @var mixed[]|null */
    protected $data;

    /**
     * MethodErrorException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param mixed[]          $data
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        int $code,
        array $data = [],
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
