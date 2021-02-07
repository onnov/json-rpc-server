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
use Onnov\JsonRpcServer\Definition\RpcGeneralDefinition;

/**
 * Class RpcDefinitionGenerator
 * @package Onnov\JsonRpcServer
 */
class RpcDefinitionGenerator
{
    /**
     * @param GeneratedDefinition $definition
     * @return string
     * @throws JsonException
     */
    public function convertToJson(GeneratedDefinition $definition): string
    {
        return  json_encode($this->objToArray($definition->toArray()), JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    private function objToArray(array $array): array
    {
        foreach ($array as $key => $val) {
            if (is_object($val) && method_exists($val, 'toArray')) {
                $array[$key] = $val->toArray();
            }

            if (is_array($array[$key])) {
                $array[$key] = $this->objToArray($array[$key]);
            }
        }

        return $array;
    }

    /**
     * @param RpcGeneralDefinition $definition
     * @param RpcFactoryInterface $factory
     * @param RpcAuthDefinition|null $auth
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
            /** @var RpcProcedureInterface $procObj */
            $procObj = $factory->get($procedure);

            /** добавим ошибку авторизации */
            if ($auth !== null && !in_array($procedure, $auth->getProcWithoutAuth(), true)) {
                $procObj
                    ->getDefinition()
                    ->setErrors(
                        [
                            $auth
                                ->getAuthError()
                                ->getCode() => $auth
                                ->getAuthError()
                        ] + $procObj->getDefinition()->getErrors() ?? []
                    );
            }

            $methods[$procedure] = $procObj->getDefinition();
        }
        $def->setMethods($methods);

        return $def;
    }
}
