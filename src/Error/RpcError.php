<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 05.02.2021
 * Time: 23:37
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Error;

use Onnov\JsonRpcServer\Definition\RpcErrorDefinition;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Class ErrorService
 * @package Onnov\JsonRpcServer\Service
 */
class RpcError
{
    /**
     * @var RpcErrorDefinition[]
     */
    private $errors;

    /**
     * ErrorService constructor.
     * @param RpcErrorDefinition $authError
     */
    public function __construct(RpcErrorDefinition $authError)
    {
        $this->errors = [
            'InvalidAuthorizeException' => $authError,
            'MethodErrorException'      => new RpcErrorDefinition(
                [
                    'code'       => 600,
                    'message'    => 'Error',
                    'errorLevel' => LogLevel::NOTICE,
                ]
            ),
            'ParseErrorException'       => new RpcErrorDefinition(
                [
                    'code'       => -32700,
                    'message'    => 'Parse error',
                    'errorLevel' => LogLevel::ERROR,
                ]
            ),
            'InvalidRequestException'   => new RpcErrorDefinition(
                [
                    'code'       => -32600,
                    'message'    => 'Invalid Request',
                    'errorLevel' => LogLevel::ERROR,
                ]
            ),
            'MethodNotFoundException'   => new RpcErrorDefinition(
                [
                    'code'       => -32601,
                    'message'    => 'Method not found',
                    'errorLevel' => LogLevel::ERROR,
                ]
            ),
            'InvalidParamsException'    => new RpcErrorDefinition(
                [
                    'code'       => -32602,
                    'message'    => 'Invalid params',
                    'errorLevel' => LogLevel::ERROR,
                ]
            ),
            'Throwable'                 => new RpcErrorDefinition(
                [
                    'code'       => -32603,
                    'message'    => 'Internal error',
                    'errorLevel' => LogLevel::EMERGENCY,
                ]
            ),
        ];
    }

    /**
     * @param string $errorName
     * @param Throwable|null $throw
     * @return RpcErrorDefinition
     */
    public function getErrorByName(string $errorName, Throwable $throw = null): RpcErrorDefinition
    {
        $error = $this->errors['Throwable'];
        if ($throw !== null && isset($this->errors[$errorName])) {
            $error = $this->errors[$errorName];

            if ($throw->getCode() !== 0) {
                $error->setCode($throw->getCode());
            }
            if ($throw->getMessage() !== '') {
                $error->setMessage($throw->getMessage());
            }
        }

        return $error;
    }

    /**
     * @param string $errorName
     * @param RpcErrorDefinition $error
     */
    public function addOrReplaceError(string $errorName, RpcErrorDefinition $error): void
    {
        $this->errors[$errorName] = $error;
    }

    /**
     * @return RpcErrorDefinition[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param RpcErrorDefinition[] $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
