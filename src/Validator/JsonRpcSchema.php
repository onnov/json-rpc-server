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

/**
 * Class JsonRpcSchema
 *
 * @package App\Validator\Schema
 */
class JsonRpcSchema
{
    /**
     * @param mixed[] $paramsSchema
     *
     * @return mixed[]
     */
    public function get(array $paramsSchema = []): array
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

        if (count($paramsSchema) > 0) {
            $params = $paramsSchema;
        }

        return [
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
        ];
    }
}
