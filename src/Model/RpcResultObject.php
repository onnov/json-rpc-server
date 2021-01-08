<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 22:00
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

use stdClass;

/**
 * Class RpcResultObject
 * @package Onnov\JsonRpcServer\Model
 */
class RpcResultObject extends RpcResultAbstract
{
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
