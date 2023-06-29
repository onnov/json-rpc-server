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

use Onnov\JsonRpcServer\Traits\JsonHelperTrait;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Schema;
use stdClass;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;

/**
 * Class JsonSchemaValidator
 *
 * @package App\Validator\JsonSchema
 */
class JsonSchemaValidator
{
    use JsonHelperTrait;

    /**
     * @var Validator 
     */
    protected $validator;

    /**
     * JsonSchemaValidator constructor.
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * @param stdClass                     $schema
     * @param stdClass|mixed[]|scalar|null $data
     * @param string                       $dataName
     */
    public function validate(stdClass $schema, $data, string $dataName = 'data'): void
    {
        if (is_array($data)) {
            $data = $this->arrayToObject($data);
        }

        // Обернем Параметры, для правильной валидации
        $dataPlus = (object)[$dataName => $data];

        // Обернем схему, для правильной валидации
        $schemaPlus = (object)[
            'type'       => 'object',
            'properties' => (object)[
                $dataName => $schema,
            ],
        ];

        $result = $this
            ->getValidator()
            ->setMaxErrors(10)
            ->validate(
                $dataPlus,
                $schemaPlus
            );

        if (!$result->isValid()) {
            throw new InvalidParamsException(
                '', //'Data validation error', // 'Ошибка валидации данных',
                0,
                null,
                (new ErrorFormatter())->formatFlat($result->error())
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
