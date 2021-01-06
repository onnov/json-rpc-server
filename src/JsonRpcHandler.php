<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use JsonException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use stdClass;
use Throwable;
use Onnov\JsonRpcServer\Exception\InvalidAuthorizeException;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;
use Onnov\JsonRpcServer\Exception\InvalidRequestException;
use Onnov\JsonRpcServer\Exception\MethodErrorException;
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
    /** @var LoggerInterface|null */
    private $logger;

    /** @var RpcService */
    private $rpcService;

    /** @var ApiExecService */
    private $apiExecService;

    /**
     * JsonRpcHandler constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(bool $responseSchemaCheck = false, LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $validator = new JsonSchemaValidator();
        $rpcSchema = new JsonRpcSchema();

        $this->rpcService = new RpcService($validator, $rpcSchema);
        $this->apiExecService = new ApiExecService($validator, $rpcSchema, $responseSchemaCheck);
    }

    /**
     * @param ApiFactoryInterface $apiFactory
     * @param string              $json
     * @param bool                $resultAuth
     * @param string[]            $methodsWithoutAuth
     *
     * @return string
     */
    public function run(
        ApiFactoryInterface $apiFactory,
        string $json,
        bool $resultAuth,
        array $methodsWithoutAuth = []
    ): string {
        $rpcService = $this->getRpcService();

        /** Парсим */
        $data = $rpcService->jsonParse($json);

        $resArr = [];
        foreach ($data as $rpc) {
            // TODO впилить паралельное выполнение, возможно amphp/amp
            $resArr[] = $this->oneRun(
                $apiFactory,
                $rpc,
                $resultAuth,
                $methodsWithoutAuth
            );
        }

        $res = implode(',', $resArr);

        if ($rpcService->isBatch()) {
            $res = '[' . $res . ']';
        }

        return $res;
    }

    /**
     * @param ApiFactoryInterface $apiFactory
     * @param stdClass            $rpc
     * @param bool                $resultAuth
     * @param string[]            $methodsWithoutAuth
     *
     * @return string
     */
    private function oneRun(
        ApiFactoryInterface $apiFactory,
        stdClass $rpc,
        bool $resultAuth,
        array $methodsWithoutAuth = []
    ): string {
        $res = [
            'jsonrpc' => '2.0',
        ];

        $errorLevel = null;

        try {
            /** валидируем и парсим JsonRPC */
            $rpcObj = $this->getRpcService()->getRpc($rpc);

            /** Проверим авторизацию */
            $this->authCheck($rpcObj->getMethod(), $resultAuth, $methodsWithoutAuth);

            /** Пытаемся выполнить запрос */
            $res['result'] = $this->getApiExeService()->exe(
                $apiFactory,
                $rpcObj
            );
        } catch (InvalidAuthorizeException $e) {
            $res['error'] = [
                'code'    => -32001,
                'message' => $e->getMessage(),
            ];
            $errorLevel = LogLevel::INFO;
        } catch (ParseErrorException $e) {
            $res['error'] = [
                'code'    => -32700,
                'message' => 'Parse error: ' . $e->getMessage(),
            ];
            $errorLevel = LogLevel::ERROR;
        } catch (InvalidRequestException $e) {
            $res['error'] = [
                'code'    => -32600,
                'message' => 'Invalid Request: ' . $e->getMessage(),
            ];
            $errorLevel = LogLevel::ERROR;
        } catch (MethodNotFoundException $e) {
            $res['error'] = [
                'code'    => -32601,
                'message' => $e->getMessage(), // 'Method not found',
            ];
            $errorLevel = LogLevel::ERROR;
        } catch (InvalidParamsException $e) {
            $res['error'] = [
                'code'    => -32602,
                'message' => $e->getMessage(), // 'Invalid params',
                'data'    => $e->getData(),
            ];
            $errorLevel = LogLevel::ERROR;
        } catch (MethodErrorException $e) {
            $res['error'] = [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
                'data'    => $e->getData(),
            ];
            $errorLevel = LogLevel::NOTICE;
        } catch (Throwable $t) {
            $res['error'] = [
                'code'    => -32603,
                'message' => 'Internal error: ' . $t->getMessage(),
                'data'    => [
                    'exception' => get_class($t),
                    'code'      => $t->getCode(),
                    'file'      => $t->getFile(),
                    'line'      => $t->getLine(),
                ]
            ];
            $errorLevel = LogLevel::EMERGENCY;
        }

        $this->log($errorLevel, $res);

        return $this->getJsonResult($rpc, $res);
    }

    /**
     * @param string $method
     * @param bool $resultAuth
     * @param string[] $methodsWithoutAuth
     */
    private function authCheck(string $method, bool $resultAuth, array $methodsWithoutAuth): void
    {
        if (
            $resultAuth === false
            && in_array($method, $methodsWithoutAuth, true) === false
        ) {
            throw new InvalidAuthorizeException(
                'Access denied, you are not authorized', // 'Доступ запрещен, вы не авторизованы'
            );
        }
    }

    /**
     * @param stdClass $rpc
     * @param mixed[] $res
     * @return string
     */
    private function getJsonResult(stdClass $rpc, array &$res): string
    {
        // ???
        $strId = 'error';
        if (isset($rpc->id)) {
            $res['id'] = $rpc->id;
            $strId = $rpc->id;
        }

        try {
            $result = json_encode($res, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $result = '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "'
                . $e->getMessage() . '"}, "id": ' . $strId . '}';
        }

        return $result;
    }

    /**
     * @param string|null $errorLevel
     * @param mixed[]     $res
     */
    private function log(?string $errorLevel, array $res): void
    {
        $logger = $this->getLogger();
        if ($errorLevel !== null && $logger instanceof LoggerInterface) {
            $error = $res['error'] ?? [];
            $logger->log($errorLevel, $error['message'], $error);
        }
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return RpcService
     */
    public function getRpcService(): RpcService
    {
        return $this->rpcService;
    }

    /**
     * @return ApiExecService
     */
    public function getApiExeService(): ApiExecService
    {
        return $this->apiExecService;
    }
}
