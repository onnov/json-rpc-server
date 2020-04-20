<?php

declare(strict_types=1);

namespace Onnov\JsonRpcServer;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Interface ApiFactoryInterface
 *
 * @package Onnov\JsonRpcServer
 */
interface ApiFactoryInterface extends ServiceSubscriberInterface, ContainerInterface
{
    /**
     * @return array
     */
    public static function getSubscribedServices(): array;

    /**
     * @param string $className
     *
     * @return bool
     */
    public function has($className): bool;

    /**
     * @param string $className
     *
     * @return mixed
     */
    public function get($className);
}
