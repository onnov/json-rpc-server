<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.02.2021
 * Time: 10:46
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Definition;

/**
 * Class RpcInfoDefinition
 * @package Onnov\JsonRpcServer\Definition
 */
class RpcInfoDefinition
{
    /**
     * Name of the api.
     *
     * @var string
     */
    private $title;

    /**
     * Description or usage information about the api.
     *
     * @var string|string[]|null
     */
    private $description = null;

    /**
     * Current version of the api.
     *
     * @var string
     */
    private $version;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
