<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Service;

use JsonMapper;
use JsonMapper_Exception;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Exception;
use Onnov\JsonRpcServer\Model\RpcModel;
use Onnov\JsonRpcServer\Validator\JsonRpcSchema;
use Onnov\JsonRpcServer\Validator\JsonSchemaValidator;
use stdClass;

/**
 * Class Rpc
 *
 * @package Onnov\JsonRpcServer
 */
class RpcService
{
    /**
     * @var bool
     */
    private $batch = true;

    /**
     * @var JsonSchemaValidator
     */
    private $validator;

    /**
     * @var JsonRpcSchema
     */
    private $rpcSchema;

    /**
     * @var JsonMapper
     */
    private $mapper;

    /**
     * RpcService constructor.
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
     * @param string $json
     *
     * @return mixed[]
     * @throws ParseErrorException
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
                false,
                512,
                JSON_THROW_ON_ERROR
            );

            if ($data instanceof stdClass) {
                $data = [$data];
                $this->setBatch(false);
            }
        } catch (Exception $e) {
            throw new ParseErrorException('', 0, $e);
        }

        return $data;
    }

    /**
     * Проверим RPC
     *
     * @param stdClass $data
     */
    private function validateJsonRpc(stdClass $data): void
    {
        $this
            ->getValidator()
            ->validate(
                $this->getRpcSchema()->get(),
                $data,
                'jsonRpc'
            );
    }

    /**
     * @param  stdClass $data
     * @return RpcModel
     */
    public function getRpc(stdClass $data): RpcModel
    {
        $this->validateJsonRpc($data);

        try {
            return $this->getMapper()->map($data, new RpcModel());
        } catch (JsonMapper_Exception $e) {
            throw new ParseErrorException('', 0, $e);
        }
    }

    /**
     * @return bool
     */
    public function isBatch(): bool
    {
        return $this->batch;
    }

    /**
     * @param bool $batch
     */
    public function setBatch(bool $batch): void
    {
        $this->batch = $batch;
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
