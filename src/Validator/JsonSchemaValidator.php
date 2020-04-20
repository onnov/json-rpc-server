<?php

/**
 * Created by PhpStorm.
 * Project: cpa
 * User: sv
 * Date: 23.03.2020
 * Time: 12:38
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Validator;

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Schema;
use stdClass;
use Onnov\JsonRpcServer\Exception\InvalidParamsException;
use Onnov\JsonRpcServer\Exception\InvalidRequestException;

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
     * @param mixed[] $arrSchema
     * @param mixed[] $arrData
     */
    public function validate(array $arrSchema, array $arrData): void
    {
        /** @var ValidationResult $result */
        $result = $this->getValidator()->schemaValidation(
            $this->arrayToObject($arrData),
            new Schema($this->arrayToObject($arrSchema)),
            10
        );

        if (!$result->isValid()) {
            /** @var ValidationError[] $errors */
            $errors = $result->getErrors();
            $data = [];
            foreach ($errors as $error) {
                $data[$error->keyword()] = [
                    $error->keywordArgs(),
                    $error->dataPointer()
                ];
            }

            throw new InvalidParamsException(
                'Ошибка валидации данных',
                $data
            );
        }
    }

    /**
     * @param mixed[] $array
     * @return string
     */
    public function arrayToJson(array $array): string
    {
        return json_encode($array);
    }

    /**
     * @param string $json
     * @return stdClass
     */
    public function jsonToObject(string $json): stdClass
    {
        $res =  json_decode($json);

        // Если не смогли преобразовать в объект значит это не json rpc запрос
        if (is_array($res)) {
            throw new InvalidRequestException(
                'The JSON sent is not a valid Request object.'
            );
        }

        return $res;
    }

    /**
     * @param mixed[] $array
     * @return stdClass|null
     */
    public function arrayToObject(array $array): ?stdClass
    {
        return $this->jsonToObject($this->arrayToJson($array));
    }

    /**
     * @return Validator
     */
    private function getValidator(): Validator
    {
        return $this->validator;
    }
}
