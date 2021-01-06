<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 23:17
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Model;

use stdClass;

/**
 * Class SimpleSchemes
 * @package Onnov\JsonRpcServer\Model
 */
class SimpleSchemes
{
    /**
     * @return stdClass
     */
    public function getNullSchema(): stdClass
    {
        return (object)['type' => 'null'];
    }

    /**
     * @return stdClass
     */
    public function getStringSchema(): stdClass
    {
        return (object)['type' => 'string'];
    }

    /**
     * @return stdClass
     */
    public function getIntSchema(): stdClass
    {
        return (object)['type' => 'int'];
    }

    /**
     * @return stdClass
     */
    public function getFloatSchema(): stdClass
    {
        return (object)['type' => 'float'];
    }

    /**
     * @return stdClass
     */
    public function getArraySchema(): stdClass
    {
        return (object)['type' => 'array'];
    }

    /**
     * @return stdClass
     */
    public function getObjectSchema(): stdClass
    {
        return (object)['type' => 'object'];
    }
}