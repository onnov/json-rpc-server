<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 22:00
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Result;

use stdClass;

/**
 * Class RpcResultObject
 * @package Onnov\JsonRpcServer\Result
 */
class RpcResultObject implements RpcResultInterface
{
    /** @var stdClass */
    protected $result;

    /**
     * RpcResultArray constructor.
     * @param stdClass $result
     */
    public function __construct(stdClass $result)
    {
        $this->result = $result;
    }

    /**
     * @return stdClass
     */
    public function getResult(): stdClass
    {
        return $this->result;
    }
}
