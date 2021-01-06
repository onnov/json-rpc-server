<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 06.01.2021
 * Time: 15:37
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Traits;

use Exception;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use stdClass;

/**
 * Class JsonHelperTrait
 * @package Onnov\JsonRpcServer\Traits
 */
trait JsonHelperTrait
{
    /**
     * @param mixed[] $array
     * @return stdClass|null
     */
    public function arrayToObject(array $array): ?stdClass
    {
        try {
            return json_decode(
                json_encode($array, JSON_THROW_ON_ERROR),
                false,
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
    }
}
