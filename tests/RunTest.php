<?php
//
///**
// * Created by PhpStorm.
// * Project: json-rpc-server
// * User: sv
// * Date: 31.12.2020
// * Time: 2:25
// */
//
//declare(strict_types=1);
//
//namespace Onnov\JsonRpcServer\Tests;
//
//use Onnov\JsonRpcServer\RpcFactoryInterface;
//use Onnov\JsonRpcServer\RpcProcedureAbstract;
//use Onnov\JsonRpcServer\RpcHandler;
//use Onnov\JsonRpcServer\Model\RpcResultSuccess;
//use Onnov\JsonRpcServer\Model\RpcRun;
//use PHPUnit\Framework\TestCase;
//
///**
// * Class RunTest
// * @package Onnov\JsonRpcServer\Tests
// */
//class RunTest extends TestCase
//{
//    /**
//     * @dataProvider jsonProvider
//     * @param string $jsonIn
//     * @param string $jsonOut
//     */
//    public function testRun(string $jsonIn, string $jsonOut): void
//    {
//        $handler = new RpcHandler();
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(),
//                $jsonIn
//            )
//        );
//
//        self::assertStringContainsString(
//            $jsonOut,
//            $res
//        );
//    }
//
//    /**
//     * @return string[][]
//     */
//    public function jsonProvider(): array
//    {
//        return [
//            ['{"jsonrpc": "2.0", "method": "test", "id": 777}', '{"jsonrpc":"2.0","result":"success","id":777}'],
//        ];
//    }
//
//    public function testParseErrorException(): void
//    {
//        $handler = new RpcHandler();
//
////        $this->expectException(ParseErrorException::class);
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(),
//                'any not json'
//            )
//        );
//
//        self::assertStringContainsString(
//            '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error: "}, "id": null}',
//            $res
//        );
//    }
//
//    public function testMethodNotFoundError(): void
//    {
//        $handler = new RpcHandler();
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(false),
//                '{"jsonrpc": "2.0", "method": "test", "id": 777}'
//            )
//        );
//
//        self::assertStringContainsString(
//            '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method \"test\" not found"},"id":777}',
//            $res
//        );
//    }
//
//    public function testValidateJsonRpcError(): void
//    {
//        $handler = new RpcHandler();
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(),
//                '{"jsonrpc": "3.0", "method": "test", "id": 777}'
//            )
//        );
//
//        self::assertStringContainsString(
//            '{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params",'
//            . '"data":{"enum":[["jsonRpc","jsonrpc"],{"expected":["2.0"]}]}},"id":777}',
//            $res
//        );
//    }
//
//    public function testAuthCheckError(): void
//    {
//        $handler = new RpcHandler();
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(),
//                '{"jsonrpc": "2.0", "method": "test", "id": 777}',
//                false
//            )
//        );
//
//        self::assertStringContainsString(
//            '{"jsonrpc":"2.0","error":{"code":-32001,"message":"Access denied, you are not authorized"},"id":777}',
//            $res
//        );
//    }
//
//    public function testMethodsWithoutAuth(): void
//    {
//        $handler = new RpcHandler();
//
//        $res = $handler->run(
//            new RpcRun(
//                $this->getFactory(),
//                '{"jsonrpc": "2.0", "method": "test", "id": 777}',
//                false,
//                ['test']
//            )
//        );
//
//        self::assertStringContainsString(
//            '{"jsonrpc":"2.0","result":"success","id":777}',
//            $res
//        );
//    }
//
//
//    /**
//     * @param bool $has
//     * @return RpcFactoryInterface
//     */
//    private function getFactory(bool $has = true): RpcFactoryInterface
//    {
//        $method = $this->createMock(RpcProcedureAbstract::class);
//        $method
//            ->method('execute')
//            ->willReturn(new RpcResultSuccess());
//
//        $factory = $this->createMock(RpcFactoryInterface::class);
//        $factory
//            ->method('has')
//            ->willReturn($has);
//        $factory
//            ->method('get')
//            ->willReturn($method);
//
//        /** @var RpcFactoryInterface $apiFactory */
//        $apiFactory = $factory;
//
//        return $apiFactory;
//    }
//}
