<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 20.04.2020
 * Time: 18:13
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Onnov\JsonRpcServer\Model\RpcResultInterface;
use stdClass;

/**
 * Interface ApiMethodInterface
 *
 * @package Onnov\JsonRpcServer
 */
interface ApiMethodInterface
{
    /**
     * @return RpcResultInterface
     */
    public function execute(): RpcResultInterface;

    /**
     * @return stdClass|null
     */
    public function requestSchema(): ?stdClass;

    /**
     * @return stdClass|null
     */
    public function responseSchema(): ?stdClass;
}
