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
 * Class RpcResultArray
 *
 * @package Onnov\JsonRpcServer\Result
 */
class RpcResultArray extends RpcResultAbstract
{
    /**
     * RpcResultArray constructor.
     *
     * @param mixed[] $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed[]
     */
    public function getResult(): array
    {
        return $this->result;
    }
}
