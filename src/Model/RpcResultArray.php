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

use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use stdClass;

/**
 * Class RpcResultAbstractNull
 * @package Onnov\JsonRpcServer\Model
 */
class RpcResultArray extends RpcResultAbstract
{
    use JsonHelperTrait;

    /**
     * RpcResultArray constructor.
     * @param mixed[] $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return stdClass
     */
    public function getResult(): stdClass
    {
        return $this->arrayToObject($this->result);
    }
}
