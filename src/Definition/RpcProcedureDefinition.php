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
 *
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcProcedureDefinition
{
    use CastableToArray;

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
     * @param  string $summary
     * @return RpcProcedureDefinition
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string|string[]|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string|string[]|null $description
     * @return RpcProcedureDefinition
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param  string[]|null $tags
     * @return RpcProcedureDefinition
     */
    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getParams(): ?stdClass
    {
        return $this->params;
    }

    /**
     * @param  stdClass|null $params
     * @return RpcProcedureDefinition
     */
    public function setParams(?stdClass $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return object|null
     */
    public function getParamsObject(): ?object
    {
        return $this->paramsObject;
    }

    /**
     * @param  object|null $paramsObject
     * @return RpcProcedureDefinition
     */
    public function setParamsObject(?object $paramsObject): self
    {
        $this->paramsObject = $paramsObject;

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getResult(): ?stdClass
    {
        return $this->result;
    }

    /**
     * @param  stdClass|null $result
     * @return RpcProcedureDefinition
     */
    public function setResult(?stdClass $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return RpcErrorDefinition[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param  RpcErrorDefinition[]|null $errors
     * @return RpcProcedureDefinition
     */
    public function setErrors(?array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }
}
