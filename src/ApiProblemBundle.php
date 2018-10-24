<?php

declare(strict_types=1);

namespace Libero\ApiProblemBundle;

use Libero\ApiProblemBundle\DependencyInjection\ApiProblemExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ApiProblemBundle extends Bundle
{
    protected function createContainerExtension() : ExtensionInterface
    {
        return new ApiProblemExtension();
    }
}
