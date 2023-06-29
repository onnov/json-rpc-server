<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use JsonException;
use JsonMapper;
use Onnov\JsonRpcServer\Definition\RpcAuthDefinition;
use Onnov\JsonRpcServer\Error\RpcError;
use Onnov\JsonRpcServer\Exception\InternalErrorException;
use Onnov\JsonRpcServer\Model\RpcRun;
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
 * Class RpcHandler
 *
 * @package Onnov\JsonRpcServer
 */
class RpcHandler
{
    private ?Throwable $throwable = null;

    private ?LoggerInterface $logger;
    private RpcService $rpcService;
    private ApiExecService $apiExecService;
    private RpcError $rpcError;
    private bool $detailError = false;

    /**
     * JsonRpcHandler constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $validator = new JsonSchemaValidator();
        $rpcSchema = new JsonRpcSchema();
        $mapper = new JsonMapper();

        $this->rpcService = new RpcService($validator, $rpcSchema, $mapper);
        $this->apiExecService = new ApiExecService($validator, $rpcSchema, $mapper);
    }

    /**
     * @param  RpcRun $rpcRun
     * @return string
     * @throws JsonException
     */
    public function run(RpcRun $rpcRun): string
    {
        if (isset($this->rpcError) === false) {
            $this->setRpcError(new RpcError($rpcRun->getAuth()->getAuthError()));
        }
        $rpcService = $this->getRpcService();

        /**
 * Парсим 
*/
        try {
            $data = $rpcService->jsonParse($rpcRun->getJson());

            $resArr = [];
            foreach ($data as $rpc) {
                switch (true) {
                case ($rpc instanceof stdClass):
                    // TODO впилить паралельное выполнение, возможно amphp/amp
                    $resArr[] = $this->oneRun(
                        $rpcRun,
                        $rpc
                    );
                    break;
                default:
                    $resArr[] = $this->getJsonStrError();
                }
            }

            $res = implode(',', $resArr);

            if ($rpcService->isBatch()) {
                $res = '[' . $res . ']';
            }
        } catch (ParseErrorException $e) {
            $res = $this->getJsonStrError($e->getPrevious());
        }

        return $res;
    }

    /**
     * @param  RpcRun   $rpcRun
     * @param  stdClass $rpc
     * @return string
     * @throws JsonException
     */
    private function oneRun(
        RpcRun $rpcRun,
        stdClass $rpc
    ): string {
        $res = [
            'jsonrpc' => '2.0',
        ];

        $error = null;

        try {
            /**
 * валидируем и парсим JsonRPC 
*/
            $rpcObj = $this->getRpcService()->getRpc($rpc);

            /**
 * Проверим авторизацию 
*/
            $this->authCheck($rpcObj->getMethod(), $rpcRun->getAuth());

            /**
 * Пытаемся выполнить запрос 
*/
            $res['result'] = $this->getApiExeService()->exe(
                $rpcRun,
                $rpcObj
            );
        } catch (InvalidAuthorizeException | MethodNotFoundException $e) {
            $this->setThrowable($e);
            $error = $this
                ->getRpcError()
                ->getErrorByName($this->getExceptionName($e), $e);
        } catch (
            InternalErrorException
            | InvalidParamsException
            | InvalidRequestException
            | MethodErrorException
            | ParseErrorException $e
        ) {
            $this->setThrowable($e);
            $error = $this
                ->getRpcError()
                ->getErrorByName($this->getExceptionName($e), $e);

            if ($e->getData() !== null) {
                $error->setData((object)$e->getData());
            }
        } catch (Throwable $t) {
            $this->setThrowable($t);
            $error = $this
                ->getRpcError()
                ->getErrorByName('Throwable');
            $error->setData(
                (object)[
                'exception' => get_class($t),
                'code'      => $t->getCode(),
                'message'   => $t->getMessage(),
                'file'      => $t->getFile(),
                'line'      => $t->getLine(),
                ]
            );
        }

        if ($error !== null) {
            $res['error'] = (object)[
                'code' => $error->getCode(),
                'message' => $error->getMessage(),
            ];
            if ($this->isDetailError() && $error->getData() !== null) {
                $res['error']->data = $error->getData();
            }
            $this->log($error->getLogLevel(), $res);
        }

        return $this->getJsonResult($rpc, $res);
    }

    /**
     * @param  Throwable $throw
     * @return string
     */
    private function getExceptionName(Throwable $throw): string
    {
        $exception = get_class($throw);

        return substr($exception, (int)strrpos($exception, '\\') + 1);
    }

    /**
     * @param string            $method
     * @param RpcAuthDefinition $auth
     */
    private function authCheck(string $method, RpcAuthDefinition $auth): void
    {
        if ($auth->isResultAuth() === false
            && in_array($method, $auth->getProcWithoutAuth(), true) === false
        ) {
            throw new InvalidAuthorizeException();
        }
    }

    /**
     * @param  stdClass $rpc
     * @param  mixed[]  $res
     * @return string
     * @throws JsonException
     */
    private function getJsonResult(stdClass $rpc, array &$res): string
    {
        if (isset($rpc->id)) {
            $res['id'] = $rpc->id;
        }

        try {
            $result = json_encode($res, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $result = $this->getJsonStrError($e, $rpc->id ?? 'error');
        }

        return $result;
    }

    /**
     * @param  Throwable|null $err
     * @param  mixed          $id
     * @return string
     * @throws JsonException
     */
    private function getJsonStrError(Throwable $err = null, $id = 'error'): string
    {
        if (gettype($id) === 'string') {
            $id = '"' . $id . '"';
        }

        $data = '';
        $msg = 'Parse error';
        if ($err !== null) {
            $data = ', "data": {"code": ' . $err->getCode() . ', "message": "' . $err->getMessage() . '"}';
            $msg = 'Parse error (' . $err->getMessage() . ')';
        }

        $res = [
            '{"jsonrpc": "2.0",',
            '"error":',
            '{"code": -32700, "message": "Parse error"' . $data . '},',
            '"id": ' . $id . '}',
        ];

        $this->log(LogLevel::ERROR, ['error' => (object)['message' => $msg]]);

        return implode(' ', $res);
    }

    /**
     * @param  string|null $errorLevel
     * @param  mixed[]     $res
     * @throws JsonException
     */
    private function log(?string $errorLevel, array $res): void
    {
        $logger = $this->getLogger();
        if ($errorLevel !== null && $logger instanceof LoggerInterface) {
            $error = json_decode(
                json_encode($res['error'] ?? [], JSON_THROW_ON_ERROR),
                true
            );
            $logger->log($errorLevel, $error->message ?? 'no message', $error);
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
     * @return RpcError
     */
    public function getRpcError(): RpcError
    {
        return $this->rpcError;
    }

    /**
     * @param RpcError $rpcError
     */
    public function setRpcError(RpcError $rpcError): void
    {
        $this->rpcError = $rpcError;
    }

    /**
     * @return Throwable|null
     */
    public function getThrowable(): ?Throwable
    {
        return $this->throwable;
    }

    /**
     * @param  Throwable|null $throwable
     * @return RpcHandler
     */
    public function setThrowable(?Throwable $throwable): RpcHandler
    {
        $this->throwable = $throwable;
        return $this;
    }

    /**
     * @return ApiExecService
     */
    public function getApiExecService(): ApiExecService
    {
        return $this->apiExecService;
    }

    /**
     * @param  ApiExecService $apiExecService
     * @return RpcHandler
     */
    public function setApiExecService(ApiExecService $apiExecService): RpcHandler
    {
        $this->apiExecService = $apiExecService;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDetailError(): bool
    {
        return $this->detailError;
    }

    /**
     * @param  bool $detailError
     * @return RpcHandler
     */
    public function setDetailError(bool $detailError): RpcHandler
    {
        $this->detailError = $detailError;
        return $this;
    }
}
