<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 23.03.2020
 * Time: 12:38
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Validator;

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Schema;
use stdClass;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;

/**
 * Class JsonSchemaValidator
 * @package App\Validator\JsonSchema
 */
class JsonSchemaValidator
{
    /** @var Validator */
    protected $validator;

    /**
     * JsonSchemaValidator constructor.
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * @param stdClass $schema
     * @param stdClass $data
     */
    public function validate(stdClass $schema, stdClass $data): void
    {
        // Обернем Параметры, для правильной валидации
        $dataPlus = (object)['data' => $data];

        // Обернем схему, для правильной валидации
        $schemaPlus = (object)[
            'type'       => 'object',
            'properties' => (object)[
                'data' => $schema,
            ],
        ];

        $result = $this->getValidator()->schemaValidation(
            $dataPlus,
            new Schema($schemaPlus),
            10
        );

        if (!$result->isValid()) {
            $errors = $result->getErrors();
            $errData = [];
            foreach ($errors as $error) {
                $errData[$error->keyword()] = [
                    $error->keywordArgs(),
                    $error->dataPointer()
                ];
            }

            throw new InvalidParamsException(
                'Data validation error', // 'Ошибка валидации данных',
                $errData
            );
        }
    }

    /**
     * @return Validator
     */
    private function getValidator(): Validator
    {
        return $this->validator;
    }
}
