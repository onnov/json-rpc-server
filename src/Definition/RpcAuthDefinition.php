<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 11:39
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

use Psr\Log\LogLevel;

/**
 * Class RpcAuthDefinition
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcAuthDefinition
{
    use CastableToArray;

    /**
     * Authorisation Error.
     *
     * @var RpcErrorDefinition
     */
    private $authError;

    /**
     * Procedures not requiring authorization.
     *
     * @var string[]
     */
    private $procWithoutAuth;

    /**
     * External authorization result.
     *
     * @var bool
     */
    private $resultAuth;

    /**
     * RpcAuthDefinition constructor.
     * @param mixed[] $data
     */
    public function __construct(array $data = [])
    {
        $this->authError = $data['authError'] ?? new RpcErrorDefinition(
            [
                'code'     => -32001,
                'message'  => 'Access denied, you are not authorized', // 'Доступ запрещен, вы не авторизованы'
                'description' => 'The error occurs if the procedure requires authorization,
                 but the user is not authorized.',
                'logLevel' => LogLevel::INFO,
            ]
        );
        $this->procWithoutAuth = $data['procWithoutAuth'] ?? [];
        $this->resultAuth = $data['resultAuth'] ?? false;
    }

    /**
     * @return RpcErrorDefinition
     */
    public function getAuthError(): RpcErrorDefinition
    {
        return $this->authError;
    }

    /**
     * @param RpcErrorDefinition $authError
     * @return RpcAuthDefinition
     */
    public function setAuthError(RpcErrorDefinition $authError): self
    {
        $this->authError = $authError;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getProcWithoutAuth(): array
    {
        return $this->procWithoutAuth;
    }

    /**
     * @param string[] $procWithoutAuth
     * @return RpcAuthDefinition
     */
    public function setProcWithoutAuth(array $procWithoutAuth): self
    {
        $this->procWithoutAuth = $procWithoutAuth;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResultAuth(): bool
    {
        return $this->resultAuth;
    }

    /**
     * @param bool $resultAuth
     * @return RpcAuthDefinition
     */
    public function setResultAuth(bool $resultAuth): self
    {
        $this->resultAuth = $resultAuth;

        return $this;
    }
}
