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

use Onnov\JsonRpcServer\Definition\RpcProcedureDefinition;
use Onnov\JsonRpcServer\Result\RpcResultInterface;

/**
 * Interface RpcProcedureInterface
 *
 * @package Onnov\JsonRpcServer
 */
interface RpcProcedureInterface
{
    /**
     * @return RpcResultInterface
     */
    public function execute(): RpcResultInterface;

    /**
     * @return RpcProcedureDefinition
     */
    public function getDefinition(): RpcProcedureDefinition;
}
