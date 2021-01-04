<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 03.07.2020
 * Time: 15:37
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

/**
 * Interface RpcResultInterface
 * @package Onnov\JsonRpcServer\Model
 */
interface RpcResultInterface
{
    /**
     * @return mixed|null
     */
    public function getResult();
}
