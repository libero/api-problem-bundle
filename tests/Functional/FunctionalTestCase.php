<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class FunctionalTestCase extends KernelTestCase
{
    /** @var ContainerInterface */
    protected static $container;

    final protected static function bootKernel(array $options = []) : KernelInterface
    {
        $kernel = parent::bootKernel($options);
        if (static::$container instanceof TestContainer) {
            return $kernel;
        }
        // For Symfony < 4.1
        $container = $kernel->getContainer();
        if (!$container instanceof ContainerInterface) {
            throw new LogicException('Could not find the container');
        }
        static::$container = $container;

        return $kernel;
    }

    final protected static function ensureKernelShutdown() : void
    {
        parent::ensureKernelShutdown();
        if (static::$container instanceof ResettableContainerInterface) {
            static::$container->reset();
        }
    }
}
