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
     * @return mixed
     */
    public function arrayToObject(array $array)
    {
        try {
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
     * @param mixed[] $array
     * @return stdClass
     */
    public function assocArrToObject(array $array): stdClass
    {
        if ($this->isAssoc($array) === false) {
            throw new RuntimeException('Array is not associative');
        }

        return $this->arrayToObject($array);
    }

    /**
     * method from Kohana
     * Tests if an array is associative or not.
     *
     * @param mixed[] $array
     * @return bool
     */
    public function isAssoc(array &$array): bool
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).

        return array_keys($keys) !== $keys;
    }
}
