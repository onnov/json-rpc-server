<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 25.06.19
 * Time: 7:54
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Service;

use JsonMapper;
use JsonMapper_Exception;
use Onnov\JsonRpcServer\ApiMethodAbstract;
use Onnov\JsonRpcServer\ApiMethodInterface;
use Onnov\JsonRpcServer\Exception\InternalErrorException;
use Onnov\JsonRpcServer\Exception\MethodNotFoundException;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Onnov\JsonRpcServer\Model\RpcModel;
use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Model\RunModel;
use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;

class ApiExecService
{
    use JsonHelperTrait;

    /** @var JsonSchemaValidator */
    protected $validator;

    /** @var JsonRpcSchema */
    protected $rpcSchema;

    /** @var JsonMapper */
    private $mapper;

    /**
     * ApiExecService constructor.
     *
     * @param JsonSchemaValidator $validator
     * @param JsonRpcSchema $rpcSchema
     * @param JsonMapper $mapper
     */
    public function __construct(
        JsonSchemaValidator $validator,
        JsonRpcSchema $rpcSchema,
        JsonMapper $mapper
    ) {
        $this->validator = $validator;
        $this->rpcSchema = $rpcSchema;
        $this->mapper = $mapper;
    }

    /**
     * @param RunModel $model
     * @param RpcModel $rpc
     *
     * @return mixed
     */
    public function exe(
        RunModel $model,
        RpcModel $rpc
    ) {
        $factory = $model->getApiFactory();
        $method = $rpc->getMethod();
        /** Проверим существование метода */
        if ($factory->has($method) === false) {
            throw new MethodNotFoundException(
                'Method "' . $method . '" not found'
            );
        }

        /** Создаем экземпляр класса
         *
         * @var ApiMethodAbstract $class
         */
        $class = $factory->get($method);

        /** Проверим соответствие интерфейсу */
        $interfaces = (array)class_implements($class);
        if (
            (bool)$interfaces === false
            || in_array(ApiMethodInterface::class, $interfaces, true) === false
        ) {
            throw new InternalErrorException(
                'Method "' . $method . '" does not match Interface'
            );
        }

        /** Валидируем парамертры ЗАПРОСА */
        if ($class->requestSchema() !== null) {
            $this->getValidator()->validate(
                $class->requestSchema(),
                $rpc->getParams(),
                'requestParams'
            );
        }

        $paramsObject = null;
        if ($class->customParamsObject() !== null) {
            try {
                $paramsObject = $this->getMapper()->map($rpc->getParams(), $class->customParamsObject());
            } catch (JsonMapper_Exception $e) {
                throw new ParseErrorException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getPrevious()
                );
            }
        }

        /** засетим в метод RpcRequest */
        $class->setRpcRequest(new RpcRequest($rpc, $paramsObject));

        /** Выполним метод */
        $res = $class->execute()->getResult();

        if ($model->isResponseCheck() && $class->responseSchema() !== null) {
            /** Валидируем парамертры ОТВЕТА */
            $this->getValidator()->validate(
                $class->responseSchema(),
                $res,
                'responseParams'
            );
        }

        return $res;
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

    /**
     * @return JsonMapper
     */
    public function getMapper(): JsonMapper
    {
        return $this->mapper;
    }
}
