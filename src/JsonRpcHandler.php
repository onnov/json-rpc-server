<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Onnov\JsonRpcServer\Exception\InvalidAuthorizeException;
use Exception;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;
use Onnov\JsonRpcServer\Exception\InvalidRequestException;
use Onnov\JsonRpcServer\Exception\MethodNotFoundException;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Onnov\JsonRpcServer\Service\ApiExecService;
use Onnov\JsonRpcServer\Service\RpcService;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;

/**
 * Class JsonRpcHandler
 *
 * @package Onnov\JsonRpcServer
 */
class JsonRpcHandler
{
    /** @var RpcService */
    private $rpc;

    /** @var ApiExecService */
    private $apiExec;

    /**
     * JsonRpcHandler constructor.
     */
    public function __construct()
    {
        $validator = new JsonSchemaValidator();
        $rpcSchema = new JsonRpcSchema();

        $this->rpc = new RpcService($validator, $rpcSchema);
        $this->apiExec = new ApiExecService($validator, $rpcSchema);
    }

    /**
     * @param ApiFactoryInterface $apiFactory
     * @param string              $json
     * @param bool                $resultAuth
     * @param string[]            $methodsWithoutAuth
     * @param bool                $responseSchemaCheck
     *
     * @return string
     */
    public function run(
        ApiFactoryInterface $apiFactory,
        string $json,
        bool $resultAuth,
        array $methodsWithoutAuth = [],
        bool $responseSchemaCheck = false
    ): string {
        $rpc = [];
        $res = [
            'jsonrpc' => '2.0',
        ];

        try {
            /** Парсим и валидируем JsonRPC */
            $rpc = $this->getRpc()->jsonParse($json);

            /** Проверим авторизацию */
            if ($resultAuth === false
                && in_array(
                    $rpc['method'],
                    $methodsWithoutAuth
                )
            ) {
                throw new InvalidAuthorizeException(
                    'Доступ запрещен, вы не авторизованы'
                );
            }

            /** Пытаемся выполнить запрос */
            $res['result'] = $this->getApiExec()->exe(
                $apiFactory,
                $rpc,
                $responseSchemaCheck
            );
        } catch (InvalidAuthorizeException $e) {
            $res['error'] = [
                'code'    => -32001,
                'message' => $e->getMessage(),
            ];
        } catch (ParseErrorException $e) {
            $res['error'] = [
                'code'    => -32700,
                'message' => 'Parse error: ' . $e->getMessage(),
            ];
        } catch (InvalidRequestException $e) {
            $res['error'] = [
                'code'    => -32600,
                'message' => 'Invalid Request: ' . $e->getMessage(),
            ];
        } catch (MethodNotFoundException $e) {
            $res['error'] = [
                'code'    => -32601,
                'message' => $e->getMessage(), // 'Method not found',
            ];
        } catch (InvalidParamsException $e) {
            $res['error'] = [
                'code'    => -32602,
                'message' => $e->getMessage(), // 'Invalid params',
                'data'    => $e->getData(),
            ];
        } catch (Exception $e) {
            $res['error'] = [
                'code'    => -32603,
                'message' => 'Internal error: ' . $e->getMessage(),
                'data'    => [
                    'exception' => get_class($e),
                    'code'      => $e->getCode(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]
            ];
        }

        if (isset($rpc['id'])) {
            $res['id'] = $rpc['id'];
        }

        return json_encode($res);
    }

    /**
     * @return RpcService
     */
    public function getRpc(): RpcService
    {
        return $this->rpc;
    }

    /**
     * @return ApiExecService
     */
    public function getApiExec(): ApiExecService
    {
        return $this->apiExec;
    }
}
