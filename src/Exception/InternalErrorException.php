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
use Throwable;

/**
 * Class InternalErrorException
 *
 * @package Onnov\JsonRpcServer\Exception
 */
class InternalErrorException extends RuntimeException
{
    /** @var mixed[] */
    protected $data;

    /**
     * InternalErrorException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param mixed[]|null $data
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        array $data = null
    ) {
        if ($previous !== null && $data === null) {
            $this->data = [
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
     * @return mixed[]|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
