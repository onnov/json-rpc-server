<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 07.02.2021
 * Time: 11:08
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use JsonException;
use Onnov\JsonRpcServer\Definition\GeneratedDefinition;
use Onnov\JsonRpcServer\Definition\RpcAuthDefinition;
use Onnov\JsonRpcServer\Definition\RpcErrorDefinition;
use Onnov\JsonRpcServer\Definition\RpcGeneralDefinition;

/**
 * Class RpcDefinitionGenerator
 *
 * @package Onnov\JsonRpcServer
 */
class RpcDefinitionGenerator
{
    /**
     * @param  GeneratedDefinition $definition
     * @return string
     * @throws JsonException
     */
    public function convertToJson(GeneratedDefinition $definition): string
    {
        return json_encode(
            $this->convertToArray($definition),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param  GeneratedDefinition $definition
     * @return mixed[]
     */
    public function convertToArray(GeneratedDefinition $definition): array
    {
        $def = ['$schema' => $definition->getSchema()] + $definition->toArray();
        unset($def['schema']);
        $def['info'] = $definition->getInfo()->toArray();
        $def['methods'] = $definition->getMethods();

        foreach ($def['methods'] as &$method) {
            $method = $method->toArray();
            if (is_array($method['errors'])) {
                $method['errors'] = array_values($method['errors']);
                /**
                 * @var RpcErrorDefinition $error
                 */
                foreach ($method['errors'] as &$error) {
                    $error = $error->toArray();
                }
            }
            if (is_object($method['paramsObject'])) {
                $method['paramsObject'] = get_class($method['paramsObject']);
            }
        }

        return $def;
    }

    /**
     * @param  RpcGeneralDefinition   $definition
     * @param  RpcFactoryInterface    $factory
     * @param  RpcAuthDefinition|null $auth
     * @return GeneratedDefinition
     */
    public function generateObject(
        RpcGeneralDefinition $definition,
        RpcFactoryInterface $factory,
        RpcAuthDefinition $auth = null
    ): GeneratedDefinition {
        $def = new GeneratedDefinition();
        $def
            ->setJrgen($definition->getJrgen())
            ->setJsonrpc($definition->getJsonrpc())
            ->setInfo($definition->getInfo())
            ->setDefinitions($definition->getDefinitions());

        $procedures = array_keys($factory::getSubscribedServices());
        sort($procedures);
        $methods = [];
        foreach ($procedures as $procedure) {
            /**
             * @var RpcProcedureInterface $procObj
             */
            $procObj = $factory->get($procedure);
            $procDef = $procObj->getDefinition();

            /**
             * добавим ошибку авторизации
             */
            if ($auth !== null && !in_array($procedure, $auth->getProcWithoutAuth(), true)) {
                $procDef
                    ->setErrors(
                        [
                            $auth
                                ->getAuthError()
                                ->getCode() => $auth
                                ->getAuthError()
                        ] + ($procDef->getErrors() ?? [])
                    );
            }

            $methods[$procedure] = $procDef;
        }

        $def->setMethods($methods);

        return $def;
    }
}
