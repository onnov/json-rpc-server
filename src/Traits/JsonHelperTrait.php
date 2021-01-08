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
use RuntimeException;
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
    public function assocArrToObject(array $array): ?stdClass
    {
        try {
            if ($this->isAssoc($array) === false) {
                throw new RuntimeException('Array is not associative');
            }

            return json_decode(
                json_encode($array, JSON_THROW_ON_ERROR),
                false,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception $e) {
            throw new ParseErrorException('', 0, $e->getPrevious());
        }
    }

    /**
     * @param mixed[] $data
     * @return bool
     */
    public function isAssoc(array &$data): bool
    {
        return array_keys($data) !== range(0, count($data) - 1);
    }
}
