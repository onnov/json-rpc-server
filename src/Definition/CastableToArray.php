<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 07.02.2021
 * Time: 11:36
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

/**
 * Trait CastableToArray
 * @package Onnov\JsonRpcServer\Definition
 */
trait CastableToArray
{
    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
