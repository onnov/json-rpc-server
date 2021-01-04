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
class RpcResultInt extends RpcResultAbstract
{
    /**
     * RpcResultInt constructor.
     * @param int $result
     */
    public function __construct(int $result)
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getResult(): int
    {
        return $this->result;
    }
}
