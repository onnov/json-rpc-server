<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Service;

use Onnov\JsonRpcServer\Exception\InvalidRequestException;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Exception;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;

/**
 * Class Rpc
 *
 * @package Onnov\JsonRpcServer
 */
class RpcService
{
    /** @var JsonSchemaValidator */
    private $validator;

    /** @var JsonRpcSchema */
    private $rpcSchema;

    /**
     * RpcService constructor.
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
     * @param string $json
     *
     * @return mixed[]
     * @throws ParseErrorException
     * @throws InvalidRequestException
     */
    public function jsonParse(string $json): array
    {
        /**
         * @param string $json
         *
         * @return array
         */
        try {
            $data = json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception $e) {
            throw new ParseErrorException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }

        return $data;
    }

    /**
     * Проверим RPC
     *
     * @param mixed[] $data
     */
    public function validateJsonRpc(array $data): void
    {
        $this->getValidator()->validate($this->getRpcSchema()->get(), $data);
    }

    /**
     * @param mixed[] $data
     * @return bool
     */
    public function isAssoc(array $data): bool
    {
        return array_keys($data) !== range(0, count($data) - 1);
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
