<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 10:48
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

use stdClass;

/**
 * Class RpcProcedureDefinition
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcProcedureDefinition
{
    /**
     * Short summary of what the procedure does.
     *
     * @var string
     */
    private $summary;

    /**
     * Longer description of what the procedure does.
     *
     * @var string|string[]|null
     */
    private $description = null;

    /**
     * Tags for grouping similar procedures.
     *
     * @var string[]|null
     */
    private $tags = null;

    /**
     * JSON-Schema of the procedure params.
     *
     * @var stdClass|null
     */
    private $params = null;

    /**
     * php object for mapping request params
     * or null if not required
     *
     * @var object|null
     */
    private $paramsObject = null;
    /**
     * JSON-Schema of the procedure result.
     *
     * @var stdClass|null
     */
    private $result = null;

    /**
     * Definition of possible error responses.
     *
     * @var RpcErrorDefinition[]|null
     */
    private $errors = null;

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return string|string[]|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|string[]|null $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param string[]|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return stdClass|null
     */
    public function getParams(): ?stdClass
    {
        return $this->params;
    }

    /**
     * @param stdClass|null $params
     */
    public function setParams(?stdClass $params): void
    {
        $this->params = $params;
    }

    /**
     * @return object|null
     */
    public function getParamsObject(): ?object
    {
        return $this->paramsObject;
    }

    /**
     * @param object|null $paramsObject
     */
    public function setParamsObject(?object $paramsObject): void
    {
        $this->paramsObject = $paramsObject;
    }

    /**
     * @return stdClass|null
     */
    public function getResult(): ?stdClass
    {
        return $this->result;
    }

    /**
     * @param stdClass|null $result
     */
    public function setResult(?stdClass $result): void
    {
        $this->result = $result;
    }

    /**
     * @return RpcErrorDefinition[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param RpcErrorDefinition[]|null $errors
     */
    public function setErrors(?array $errors): void
    {
        $this->errors = $errors;
    }
}
