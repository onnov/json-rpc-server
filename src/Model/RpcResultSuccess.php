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
class RpcResultSuccess extends RpcResultAbstract
{
    public function __construct()
    {
        $this->result = 'success';
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
