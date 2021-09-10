<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 20.04.2020
 * Time: 18:13
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use stdClass;

/**
 * Interface ApiMethodInterface
 *
 * @package Onnov\JsonRpcServer
 */
interface ApiMethodInterface
{
    /**
     * @return stdClass|array|string|int|float|null
     */
    public function execute();

    /**
     * @return mixed[]
     */
    public function requestSchema(): array;

    /**
     * @return mixed[]
     */
    public function responseSchema(): array;
}
