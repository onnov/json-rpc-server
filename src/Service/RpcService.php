<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Service;

use Onnov\JsonRpcServer\Exception\InternalErrorException;
use Onnov\JsonRpcServer\Exception\InvalidAuthorizeException;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;
use Onnov\JsonRpcServer\Exception\InvalidRequestException;
use Onnov\JsonRpcServer\Exception\MethodNotFoundException;
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
     * @return array
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

        /** Проверим RPC */
        $this->getValidator()->validate($this->getRpcSchema()->get(), $data);

        return $data;
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
