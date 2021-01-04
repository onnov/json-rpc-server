<?php

/**
 * Created by PhpStorm.
 * Project: json-rpc-server
 * User: sv
 * Date: 31.12.2020
 * Time: 2:25
 */

declare(strict_types=1);

namespace Onnov\JsonRpcServer\Tests;

use Onnov\JsonRpcServer\ApiFactoryInterface;
use Onnov\JsonRpcServer\ApiMethodAbstract;
use Onnov\JsonRpcServer\Exception\ParseErrorException;
use Onnov\JsonRpcServer\JsonRpcHandler;
use Onnov\JsonRpcServer\Model\RpcResultSuccess;
use PHPUnit\Framework\TestCase;

/**
 * Class RunTest
 * @package Onnov\JsonRpcServer\Tests
 */
class RunTest extends TestCase
{
    /**
     * @dataProvider jsonProvider
     * @param string $jsonIn
     * @param string $jsonOut
     */
    public function testRun(string $jsonIn, string $jsonOut): void
    {
        $handler = new JsonRpcHandler();

        $res = $handler->run(
            $this->getFactory(),
            $jsonIn,
            true
        );

        self::assertStringContainsString(
            $jsonOut,
            $res
        );
    }

    /**
     * @return string[][]
     */
    public function jsonProvider(): array
    {
        return [
            ['{"jsonrpc": "2.0", "method": "test", "id": 777}', '{"jsonrpc":"2.0","result":"success","id":777}'],
        ];
    }

    public function testParseErrorException(): void
    {
        $handler = new JsonRpcHandler();

        $this->expectException(ParseErrorException::class);

        $handler->run(
            $this->getFactory(),
            'any not json',
            true
        );
    }

    public function testMethodNotFoundError(): void
    {
        $handler = new JsonRpcHandler();

        $res = $handler->run(
            $this->getFactory(false),
            '{"jsonrpc": "2.0", "method": "test", "id": 777}',
            true
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method \"test\" not found"},"id":777}',
            $res
        );
    }

    public function testValidateJsonRpcError(): void
    {
        $handler = new JsonRpcHandler();

        $res = $handler->run(
            $this->getFactory(),
            '{"jsonrpc": "3.0", "method": "test", "id": 777}',
            true
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32602,"message":"Data validation error",'
            . '"data":{"enum":[{"expected":["2.0"]},["jsonrpc"]]}},"id":777}',
            $res
        );
    }

    public function testAuthCheckError(): void
    {
        $handler = new JsonRpcHandler();

        $res = $handler->run(
            $this->getFactory(),
            '{"jsonrpc": "2.0", "method": "test", "id": 777}',
            false
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32001,"message":"Access denied, you are not authorized"},"id":777}',
            $res
        );
    }

    public function testMethodsWithoutAuth(): void
    {
        $handler = new JsonRpcHandler();

        $res = $handler->run(
            $this->getFactory(),
            '{"jsonrpc": "2.0", "method": "test", "id": 777}',
            false,
            ['test']
            //           $responseSchemaCheck
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","result":"success","id":777}',
            $res
        );
    }


    /**
     * @param bool $has
     * @return ApiFactoryInterface
     */
    private function getFactory(bool $has = true): ApiFactoryInterface
    {
        $method = $this->createMock(ApiMethodAbstract::class);
        $method
            ->method('execute')
            ->willReturn(new RpcResultSuccess());

        $factory = $this->createMock(ApiFactoryInterface::class);
        $factory
            ->method('has')
            ->willReturn($has);
        $factory
            ->method('get')
            ->willReturn($method);

        /** @var ApiFactoryInterface $apiFactory */
        $apiFactory = $factory;

        return $apiFactory;
    }
}
