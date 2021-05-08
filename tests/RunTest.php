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

use Onnov\JsonRpcServer\Definition\RpcAuthDefinition;
use Onnov\JsonRpcServer\RpcFactoryInterface;
use Onnov\JsonRpcServer\RpcProcedureAbstract;
use Onnov\JsonRpcServer\RpcHandler;
use Onnov\JsonRpcServer\Result\RpcResultSuccess;
use Onnov\JsonRpcServer\Model\RpcRun;
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
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => $jsonIn,
                ]
            )
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
        $handler = new RpcHandler();

//        $this->expectException(ParseErrorException::class);

        self::assertStringContainsString(
            implode(
                ',',
                [
                    '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"',
                    ' "data" => {"code": 4, "message": "Syntax error"}}, "id": "error"}'
                ]
            ),
            $handler->run(
                new RpcRun(
                    [
                        'rpcFactory' => $this->getFactory(),
                        'json'       => 'any not json',
                    ]
                )
            )
        );
    }

    public function testMethodNotFoundError(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(false),
                    'json'       => '{"jsonrpc": "2.0", "method": "test", "id": 777}',
                ]
            )
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method \"test\" not found."},"id":777}',
            $res
        );
    }

    public function testValidateJsonRpcError(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '{"jsonrpc": "3.0", "method": "test", "id": 777}',
                ]
            )
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params",'
            . '"data":{"enum":[["jsonRpc","jsonrpc"],{"expected":["2.0"]}]}},"id":777}',
            $res
        );
    }

    public function testAuthCheckError(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '{"jsonrpc": "2.0", "method": "test", "id": 777}',
                    'auth' => new RpcAuthDefinition(['resultAuth' => false])
                ]
            )
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","error":{"code":-32001,"message":"Access denied, you are not authorized"},"id":777}',
            $res
        );
    }

    public function testMethodsWithoutAuth(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '{"jsonrpc": "2.0", "method": "test", "id": 777}',
                    'auth' => new RpcAuthDefinition(['resultAuth' => false, 'procWithoutAuth' => ['test']]),
                ]
            )
        );

        self::assertStringContainsString(
            '{"jsonrpc":"2.0","result":"success","id":777}',
            $res
        );
    }

    public function testBatch(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '[{"jsonrpc": "2.0", "method": "test", "id": 777},'
                        . ' {"jsonrpc": "2.0", "method": "test", "id": 888}]',
                ]
            )
        );

        self::assertStringContainsString(
            '[{"jsonrpc":"2.0","result":"success","id":777},{"jsonrpc":"2.0","result":"success","id":888}]',
            $res
        );
    }

    public function testBatchParseError(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '[{"jsonrpc": "2.0", "method": "test", "id": 777}, "any not json"]',
                ]
            )
        );

        self::assertStringContainsString(
            '[{"jsonrpc":"2.0","result":"success","id":777},'
            . '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": "error"}]',
            $res
        );
    }

    public function testBatchNotRpc(): void
    {
        $handler = new RpcHandler();

        $res = $handler->run(
            new RpcRun(
                [
                    'rpcFactory' => $this->getFactory(),
                    'json'       => '[{"jsonrpc": "2.0", "method": "test", "id": 777}, {"method": "test", "id": 777}]',
                ]
            )
        );

        self::assertStringContainsString(
            '[{"jsonrpc":"2.0","result":"success","id":777},'
            . '{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params",'
            . '"data":{"required":[["jsonRpc"],{"missing":"jsonrpc"}]}},"id":777}]',
            $res
        );
    }

    /**
     * @param bool $has
     * @return RpcFactoryInterface
     */
    private function getFactory(bool $has = true): RpcFactoryInterface
    {
        $method = $this->createMock(RpcProcedureAbstract::class);
        $method
            ->method('execute')
            ->willReturn(new RpcResultSuccess());

        $factory = $this->createMock(RpcFactoryInterface::class);
        $factory
            ->method('has')
            ->willReturn($has);
        $factory
            ->method('get')
            ->willReturn($method);

        /** @var RpcFactoryInterface $apiFactory */
        $apiFactory = $factory;

        return $apiFactory;
    }
}
