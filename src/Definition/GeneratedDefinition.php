<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 11:01
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

/**
 * Class GeneratedDefinition
 * @package Onnov\JsonRpcServer\Definition
 */
class GeneratedDefinition extends RpcGeneralDefinition
{
    /**
     * Definitions of the available procedures in the api.
     * A key equals to the name of a procedure.
     *
     * @var RpcProcedureDefinition[]
     */
    private $methods;

    /**
     * @return RpcProcedureDefinition[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param RpcProcedureDefinition[] $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }
}
