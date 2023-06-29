<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 03.07.2020
 * Time: 15:32
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Result;

/**
 * Class RpcResultString
 *
 * @package Onnov\JsonRpcServer\Result
 */
class RpcResultString extends RpcResultAbstract
{
    /**
     * RpcResultString constructor.
     *
     * @param string $result
     */
    public function __construct(string $result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
