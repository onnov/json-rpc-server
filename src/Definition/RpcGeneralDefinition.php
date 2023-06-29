<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 10:47
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

use stdClass;

/**
 * Class RpcGeneralDefinition
 *
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcGeneralDefinition
{
    use CastableToArray;

    /**
     * https://github.com/mzernetsch/jrgen
     *
     * @var string
     */
    private $schema = 'https://rawgit.com/mzernetsch/jrgen/master/jrgen-spec.schema.json';

    /**
     * Version of the jrgen spec.
     *
     * @var string
     */
    private $jrgen = '1.1';

    /**
     * Version of the json-rpc protocol.
     *
     * @var string
     */
    private $jsonrpc = '2.0';

    /**
     * Meta information about the api.
     *
     * @var RpcInfoDefinition
     */
    private $info;

    /**
     * Global definitions for use in the api
     *
     * @var stdClass|null
     */
    private $definitions = null;

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     */
    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getJrgen(): string
    {
        return $this->jrgen;
    }

    /**
     * @param  string $jrgen
     * @return RpcGeneralDefinition
     */
    public function setJrgen(string $jrgen): self
    {
        $this->jrgen = $jrgen;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    /**
     * @param  string $jsonrpc
     * @return RpcGeneralDefinition
     */
    public function setJsonrpc(string $jsonrpc): self
    {
        $this->jsonrpc = $jsonrpc;

        return $this;
    }

    /**
     * @return RpcInfoDefinition
     */
    public function getInfo(): RpcInfoDefinition
    {
        return $this->info;
    }

    /**
     * @param  RpcInfoDefinition $info
     * @return RpcGeneralDefinition
     */
    public function setInfo(RpcInfoDefinition $info): self
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getDefinitions(): ?stdClass
    {
        return $this->definitions;
    }

    /**
     * @param  stdClass|null $definitions
     * @return RpcGeneralDefinition
     */
    public function setDefinitions(?stdClass $definitions): self
    {
        $this->definitions = $definitions;

        return $this;
    }
}
