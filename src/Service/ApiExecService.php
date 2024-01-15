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
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Onnov\JsonRpcServer\Exception\RpcArrayException;
use Onnov\JsonRpcServer\RpcProcedureInterface;
use Onnov\JsonRpcServer\Exception\MethodErrorException;
use Onnov\JsonRpcServer\Exception\MethodNotFoundException;
use Onnov\JsonRpcServer\Exception\RpcNumberException;
use Onnov\JsonRpcServer\Model\RpcModel;
use Onnov\JsonRpcServer\Model\RpcRequest;
use Onnov\JsonRpcServer\Model\RpcRun;
use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;

class ApiExecService
{
    use JsonHelperTrait;

    /**
     * @var JsonSchemaValidator
     */
    protected $validator;

    /**
     * @var JsonRpcSchema
     */
    protected $rpcSchema;

    /**
     * @var JsonMapper
     */
    private $mapper;

    /**
     * ApiExecService constructor.
     *
     * @param JsonSchemaValidator $validator
     * @param JsonRpcSchema       $rpcSchema
     * @param JsonMapper          $mapper
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
     * @param RpcRun   $model
     * @param RpcModel $rpc
     *
     * @return mixed
     */
    public function exe(
        RpcRun $model,
        RpcModel $rpc
    ) {
        $factory = $model->getRpcFactory();
        $method = $rpc->getMethod();
        /**
         * Проверим существование метода
         */
        if ($factory->has($method) === false) {
            throw new MethodNotFoundException(
                'Method "' . $method . '" not found.'
            );
        }

        /**
         * Создаем экземпляр класса
         *
         * @var RpcProcedureInterface $class
         */
        $class = $factory->get($method);

        //        /** Проверим соответствие интерфейсу */
        //        $this->checkInterface($class, $method);

        /**
         * Валидируем парамертры ЗАПРОСА
         */
        if ($class->getDefinition()->getParams() !== null) {
            $this->getValidator()->validate(
                $class->getDefinition()->getParams(),
                $rpc->getParams(),
                'requestParams'
            );
        }

        $paramsObject = null;
        if ($class->getDefinition()->getParamsObject() !== null) {
            try {
                $paramsObject = $this
                    ->getMapper()
                    ->map(
                        $rpc->getParams(),
                        $class->getDefinition()->getParamsObject()
                    );
            } catch (JsonMapper_Exception $e) {
                throw new ParseErrorException('', 0, $e->getPrevious());
            }
        }

        /**
         * засетим в метод RpcRequest
         */
        if (method_exists($class, 'setRpcRequest')) {
            $class->setRpcRequest(new RpcRequest($rpc, $paramsObject));
        }

        /**
         * Выполним метод
         */
        try {
            $res = $class->execute()->getResult();
        } catch (RpcNumberException $e) {
            $code = 0;
            $message = 'Unknown error';
            $data = null;
            $errors = $class->getDefinition()->getErrors();
            if ($errors !== null && isset($errors[$e->getCode()])) {
                $err = $errors[$e->getCode()];
                $code = $err->getCode();
                $message = $err->getMessage();
                $data = $err->getData();
            }
            throw new MethodErrorException($message, $code, $e, $data);
        } catch (RpcArrayException $e) {
            throw new MethodErrorException($e->getMessage(), $e->getCode(), $e, $e->getData());
        }

        if ($model->isResponseCheck() && $class->getDefinition()->getResult() !== null) {
            /**
             * Валидируем парамертры ОТВЕТА
             */
            $this->getValidator()->validate(
                $class->getDefinition()->getResult(),
                $res,
                'responseParams'
            );
        }

        return $res;
    }

    //    /**
    //     * @param RpcProcedureInterface $class
    //     * @param string $method
    //     */
    //    private function checkInterface(RpcProcedureInterface $class, string $method): void
    //    {
    //        // ???
    //        $interfaces = (array)class_implements($class);
    //        if (
    //            (bool)$interfaces === false
    //            || in_array(RpcProcedureInterface::class, $interfaces, true) === false
    //        ) {
    //            throw new InternalErrorException(
    //                'Method "' . $method . '" does not match Interface.'
    //            );
    //        }
    //    }

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
