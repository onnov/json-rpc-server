<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use JsonException;
use JsonMapper;
use Onnov\JsonRpcServer\Exception\InternalErrorException;
use Onnov\JsonRpcServer\Model\RunModel;
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

    /** @var mixed[] */
    private $errors = [
        'InvalidAuthorizeException' => [
            'code'       => -32001,
            'message'    => 'Access denied, you are not authorized', // 'Доступ запрещен, вы не авторизованы'
            'errorLevel' => LogLevel::INFO,
        ],
        'ParseErrorException'       => [
            'code'       => -32700,
            'message'    => 'Parse error',
            'errorLevel' => LogLevel::ERROR,
        ],
        'InvalidRequestException'   => [
            'code'       => -32600,
            'message'    => 'Invalid Request',
            'errorLevel' => LogLevel::ERROR,
        ],
        'MethodNotFoundException'   => [
            'code'       => -32601,
            'message'    => 'Method not found',
            'errorLevel' => LogLevel::ERROR,
        ],
        'InvalidParamsException'    => [
            'code'       => -32602,
            'message'    => 'Invalid params',
            'errorLevel' => LogLevel::ERROR,
        ],
        'MethodErrorException'      => [
            'code'       => 600,
            'message'    => 'Error',
            'errorLevel' => LogLevel::NOTICE,
        ],
        'Throwable'                 => [
            'code'       => -32603,
            'message'    => 'Internal error',
            'errorLevel' => LogLevel::EMERGENCY,
        ],
    ];

    /**
     * JsonRpcHandler constructor.
     * @param LoggerInterface|null $logger
     * @param mixed[]|null $errors
     */
    public function __construct(LoggerInterface $logger = null, array $errors = null)
    {
        $this->logger = $logger;

        if ($errors !== null) {
            $this->errors = array_merge($this->errors, $errors);
        }

        $validator = new JsonSchemaValidator();
        $rpcSchema = new JsonRpcSchema();
        $mapper = new JsonMapper();

        $this->rpcService = new RpcService($validator, $rpcSchema, $mapper);
        $this->apiExecService = new ApiExecService($validator, $rpcSchema, $mapper);
    }

    /**
     * @param RunModel $model
     * @return string
     */
    public function run(RunModel $model): string
    {
        $rpcService = $this->getRpcService();

        /** Парсим */
        try {
            $data = $rpcService->jsonParse($model->getJson());
        } catch (ParseErrorException $e) {
            $msg = 'Parse error: ' . $e->getMessage();

            $this->log(LogLevel::ERROR, ['error' => ['message' => $msg]]);

            return '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "' . $msg . '"}, "id": null}';
        }

        $resArr = [];
        foreach ($data as $rpc) {
            // TODO впилить паралельное выполнение, возможно amphp/amp
            $resArr[] = $this->oneRun(
                $model,
                $rpc
            );
        }

        $res = implode(',', $resArr);

        if ($rpcService->isBatch()) {
            $res = '[' . $res . ']';
        }

        return $res;
    }

    /**
     * @param RunModel $model
     * @param stdClass $rpc
     * @return string
     */
    private function oneRun(
        RunModel $model,
        stdClass $rpc
    ): string {
        $res = [
            'jsonrpc' => '2.0',
        ];

        $err = $this->getErrors();

        $errorLevel = null;

        try {
            /** валидируем и парсим JsonRPC */
            $rpcObj = $this->getRpcService()->getRpc($rpc);

            /** Проверим авторизацию */
            $this->authCheck($rpcObj->getMethod(), $model->isResultAuth(), $model->getMethodsWithoutAuth());

            /** Пытаемся выполнить запрос */
            $res['result'] = $this->getApiExeService()->exe(
                $model,
                $rpcObj
            );
        } catch (InvalidAuthorizeException | MethodNotFoundException $e) {
            $eName = $this->getExceptionName($e);

            $res['error'] = [
                'code'    => $this->getCode($e, $eName),
                'message' => $this->getMessage($e, $eName),
            ];
            $errorLevel = $err[$eName]['errorLevel'] ?? LogLevel::ERROR;
        } catch (
            InternalErrorException
            | InvalidParamsException
            | InvalidRequestException
            | MethodErrorException
            | ParseErrorException $e
        ) {
            $eName = $this->getExceptionName($e);

            $res['error'] = [
                'code'    => $this->getCode($e, $eName),
                'message' => $this->getMessage($e, $eName),
                'data'    => $e->getData()
            ];
            $errorLevel = $err[$eName]['errorLevel'] ?? LogLevel::ERROR;
        } catch (Throwable $t) {
            $eName = 'Throwable';
            $res['error'] = [
                'code'    => $this->getCode($t, $eName),
                'message' => $this->getMessage($t, $eName),
                'data'    => [
                    'exception' => get_class($t),
                    'code'      => $t->getCode(),
                    'message'   => $t->getMessage(),
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
     * @param Throwable $throw
     * @param string $exceptionMame
     * @return int
     */
    private function getCode(Throwable $throw, string $exceptionMame): int
    {
        $err = $this->getErrors();

        return $throw->getCode() !== 0 ? $throw->getCode() : $err[$exceptionMame]['code'] ?? 0;
    }

    /**
     * @param Throwable $throw
     * @param string $exceptionMame
     * @return string
     */
    private function getMessage(Throwable $throw, string $exceptionMame): string
    {
        $err = $this->getErrors();

        return $throw->getMessage() !== '' ? $throw->getMessage() : $err[$exceptionMame]['message'] ?? '';
    }

    /**
     * @param Throwable $throw
     * @return string
     */
    private function getExceptionName(Throwable $throw): string
    {
        $exception = get_class($throw);

        return substr($exception, (int)strrpos($exception, '\\') + 1);
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
            $this->log(LogLevel::ERROR, ['error' => ['message' => $e->getMessage()]]);

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

    /**
     * @return mixed[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
