<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 19:17
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

use Onnov\JsonRpcServer\ApiFactoryInterface;

/**
 * Class RunModel
 * @package Onnov\JsonRpcServer\Model
 */
class RunModel
{
    /** @var ApiFactoryInterface */
    private $apiFactory;

    /** @var string */
    private $json;

    /** @var bool */
    private $resultAuth;

    /** @var string[] */
    private $methodsWithoutAuth;

    /** @var bool */
    private $responseCheck;

    /**
     * RunModel constructor.
     * @param ApiFactoryInterface $apiFactory
     * @param string $json
     * @param bool $resultAuth
     * @param string[] $methodsWithoutAuth
     * @param bool $responseCheck
     */
    public function __construct(
        ApiFactoryInterface $apiFactory,
        string $json,
        bool $resultAuth = true,
        array $methodsWithoutAuth = [],
        bool $responseCheck = false
    ) {
        $this
            ->setApiFactory($apiFactory)
            ->setJson($json)
            ->setResultAuth($resultAuth)
            ->setMethodsWithoutAuth($methodsWithoutAuth)
            ->setResponseCheck($responseCheck);
    }

    /**
     * @return ApiFactoryInterface
     */
    public function getApiFactory(): ApiFactoryInterface
    {
        return $this->apiFactory;
    }

    /**
     * @param ApiFactoryInterface $apiFactory
     * @return RunModel
     */
    public function setApiFactory(ApiFactoryInterface $apiFactory): self
    {
        $this->apiFactory = $apiFactory;

        return $this;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @param string $json
     * @return RunModel
     */
    public function setJson(string $json): self
    {
        $this->json = $json;

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
     * @return RunModel
     */
    public function setResultAuth(bool $resultAuth): self
    {
        $this->resultAuth = $resultAuth;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMethodsWithoutAuth(): array
    {
        return $this->methodsWithoutAuth;
    }

    /**
     * @param string[] $methodsWithoutAuth
     * @return RunModel
     */
    public function setMethodsWithoutAuth(array $methodsWithoutAuth): self
    {
        $this->methodsWithoutAuth = $methodsWithoutAuth;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResponseCheck(): bool
    {
        return $this->responseCheck;
    }

    /**
     * @param bool $responseCheck
     * @return RunModel
     */
    public function setResponseCheck(bool $responseCheck): self
    {
        $this->responseCheck = $responseCheck;

        return $this;
    }
}
