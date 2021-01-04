<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 03.07.2020
 * Time: 14:09
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

/**
 * Interface RpcResult
 * @package Onnov\JsonRpcServer\Model
 */
abstract class RpcResultAbstract implements RpcResultInterface
{
//    /** @var array|string|int|float|null */
    /** @var mixed|null */
    protected $result = null;
}
