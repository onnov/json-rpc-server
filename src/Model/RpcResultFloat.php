<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 03.07.2020
 * Time: 15:32
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

/**
 * Class RpcResultAbstractNull
 * @package Onnov\JsonRpcServer\Model
 */
class RpcResultFloat extends RpcResultAbstract
{
    /**
     * RpcResultString constructor.
     * @param float $result
     */
    public function __construct(float $result)
    {
        $this->result = $result;
    }

    /**
     * @return float
     */
    public function getResult(): float
    {
        return $this->result;
    }
}
