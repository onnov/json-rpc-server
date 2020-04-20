<?php

/**
 * Created by PhpStorm.
 * Project: logohost.dev
 * User: sv
 * Date: 25.06.19
 * Time: 7:54
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Service;

use Onnov\JsonRpcServer\ApiMethodInterface;
use Onnov\JsonRpcServer\ApiFactoryInterface;
use Onnov\JsonRpcServer\Exception\InternalErrorException;
use Onnov\JsonRpcServer\Exception\MethodNotFoundException;
use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;

class ApiExecService
{
    /** @var JsonSchemaValidator */
    protected $validator;

    /** @var JsonRpcSchema */
    protected $rpcSchema;

    /**
     * ApiExecService constructor.
     *
     * @param JsonSchemaValidator $validator
     * @param JsonRpcSchema       $rpcSchema
     */
    public function __construct(
        JsonSchemaValidator $validator,
        JsonRpcSchema $rpcSchema
    ) {
        $this->validator = $validator;
        $this->rpcSchema = $rpcSchema;
    }

    /**
     * @param ApiFactoryInterface $factory
     * @param array               $rpc
     * @param bool                $responseSchemaCheck
     *
     * @return mixed
     */
    public function exe(
        ApiFactoryInterface $factory,
        array $rpc,
        bool $responseSchemaCheck
    ) {
        $method = $rpc['method'];
        /** Проверим существование метода */
        if ($factory->has($method) === false) {
            throw new MethodNotFoundException(
                'Method "' . $method . '" not found'
            );
        }

        /** Создаем экземпляр класса
         *
         * @var ApiMethodInterface $class
         */
        $class = $factory->get($method);

        /** Проверим соответствие интерфейсу */
        $interfaces = class_implements($class);
        if ($interfaces === false
            || in_array(
                ApiMethodInterface::class,
                $interfaces
            ) === false
        ) {
            throw new InternalErrorException(
                'Method "' . $method . '" does not match Interface'
            );
        }

        /** Валидируем парамертры ЗАПРОСА */
        $this->getValidator()->validate(
            $this->getRpcSchema()->get(
                $class->requestSchema()
            ),
            $rpc
        );

        /** Выполним метод */
        $res = $class->execute(new RpcRequest($rpc));

        if ($responseSchemaCheck) {
            /** Валидируем парамертры ОТВЕТА */
            $this->getValidator()->validate(
                $this->getRpcSchema()->get(
                    $class->responseSchema()
                ),
                $rpc
            );
        }

        return $res;
    }

    /**
     * @param ApiFactoryInterface $factory
     * @param string              $className
     */
    private function checkClass(
        ApiFactoryInterface $factory,
        string $className
    ): void {
    }

    /**
     * @return JsonSchemaValidator
     */
    public function getValidator(): JsonSchemaValidator
    {
        return $this->validator;
    }

    /**
     * @return JsonRpcSchema
     */
    public function getRpcSchema(): JsonRpcSchema
    {
        return $this->rpcSchema;
    }
}