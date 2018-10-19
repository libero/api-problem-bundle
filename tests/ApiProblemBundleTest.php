<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle;

use Libero\ApiProblemBundle\ApiProblemBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class ApiProblemBundleTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_a_bundle() : void
    {
        $bundle = new ApiProblemBundle();

        $this->assertInstanceOf(BundleInterface::class, $bundle);
    }
}
