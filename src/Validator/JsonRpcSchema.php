<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 23.03.2020
 * Time: 12:53
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Validator;

use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use stdClass;

/**
 * Class JsonRpcSchema
 *
 * @package App\Validator\Schema
 */
class JsonRpcSchema
{
    use JsonHelperTrait;

    /**
     * @param stdClass|null $paramsSchema
     *
     * @return stdClass
     */
    public function get(stdClass $paramsSchema = null): stdClass
    {
        $params = [
            'type' => [
                'object',
                'array',
                'string',
                'number',
                'boolean',
                'null',
            ],
        ];

        if ($paramsSchema !== null) {
            $params = $paramsSchema;
        }

        return $this->arrayToObject([
            'type'                 => 'object',
            'description'          => 'json rpc 2.0 request schema',
            'additionalProperties' => false,
            'required'             => [
                'jsonrpc',
                'method',
            ],
            'properties'           => [
                'jsonrpc' => [
                    'type' => 'string',
                    'enum' => [
                        '2.0',
                    ],
                ],
                'method'  => [
                    'type'     => 'string',
                    'examples' => [
                        'Foo.bar',
                    ],
                ],
                'params'  => $params,
                'id'      => [
                    'type' => [
                        'string',
                        'null',
                        'integer',
                    ],
                ],
            ],
        ]);
    }
}
