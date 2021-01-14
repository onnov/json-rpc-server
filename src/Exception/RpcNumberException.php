<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 14.01.2021
 * Time: 21:04
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Exception;

use RuntimeException;

/**
 * Class RpcNumberException
 * @package Onnov\JsonRpcServer\Exception
 */
class RpcNumberException extends RuntimeException
{
    /**
     * RpcNumberException constructor.
     * @param int $code
     */
    public function __construct(int $code)
    {
        parent::__construct('', $code);
    }
}
