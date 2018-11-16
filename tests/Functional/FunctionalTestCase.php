<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional;

use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use tests\Libero\ApiProblemBundle\Functional\App\Kernel;

abstract class FunctionalTestCase extends TestCase
{
    /** @var Filesystem */
    private static $filesystem;

    /**
     * @var KernelInterface
     */
    private static $kernel;

    public static function setUpBeforeClass() : void
    {
        self::$filesystem = new Filesystem();
        parent::setUpBeforeClass();
        self::$kernel = self::createKernel();
    }

    public static function tearDownAfterClass() : void
    {
        parent::tearDownAfterClass();

        self::$filesystem->remove(self::$kernel->getCacheDir());
    }

    /**
     * @before
     */
    final public function resetLogger() : void
    {
        /** @var BufferingLogger $logger */
        $logger = self::getContainer()->get('logger');

        $logger->cleanLogs();
    }

    final public function getContainer() : ContainerInterface
    {
        if (!$kernel = self::$kernel->getContainer()) {
            throw new LogicException('Kernel is shut down');
        }

        return $kernel;
    }

    final public function getKernel() : KernelInterface
    {
        return self::$kernel;
    }

    private static function createKernel(array $options = []) : KernelInterface
    {
        $kernel = new Kernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true
        );
        $kernel->boot();

        return $kernel;
    }
}
