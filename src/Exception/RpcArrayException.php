<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 15.01.2024
 * Time: 21:11
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Exception;

use stdClass;
use Throwable;

/**
 * Class RpcArrayException
 *
 * @package Onnov\JsonRpcServer\Exception
 */
class RpcArrayException extends MethodErrorException
{
    /**
     * RpcArrayException constructor.
     *
     * @param array<string, mixed>          $error
     * @param Throwable|null $previous
     */
    public function __construct(array $error, Throwable $previous = null)
    {
        $message = $error['message'] ?? 'Unknown error';

        $code = (int)($error['code'] ?? 0);

        $data = null;
        if (isset($error['data']) && is_array($error['data'])) {
            $data = (object)$error['data'];
        }
        if (isset($error['data']) && $error['data'] instanceof stdClass) {
            $data = $error['data'];
        }

        parent::__construct($message, $code, $previous, $data);
    }
}
