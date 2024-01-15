<?php

/**
 * Created by PhpStorm.
 * Project: json_rpc_server
 * User: sv
 * Date: 24.06.19
 * Time: 17:55
 */

namespace Onnov\JsonRpcServer\Exception;

use RuntimeException;
use stdClass;
use Throwable;

/**
 * Class InvalidParamsException
 *
 * @package Onnov\JsonRpcServer\Exception
 */
class MethodErrorException extends RuntimeException
{
    /**
     * @var stdClass|null
     */
    protected $data;

    /**
     * InternalErrorException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param stdClass|null  $data
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        stdClass $data = null
    ) {
        if ($previous !== null && $data === null) {
            $this->data = (object)[
                'exception' => get_class($previous),
                'code'      => $previous->getCode(),
                'file'      => $previous->getFile(),
                'line'      => $previous->getLine(),
            ];
        } elseif ($data !== null) {
            $this->data = $data;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return stdClass|null
     */
    public function getData(): ?stdClass
    {
        return $this->data;
    }
}
