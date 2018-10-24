<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional\App;

use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class Controller
{
    public function success() : Response
    {
        return new Response();
    }

    public function httpException() : Response
    {
        throw new HttpException(Response::HTTP_I_AM_A_TEAPOT, 'An HTTP exception');
    }

    public function exception() : Response
    {
        throw new Exception('An exception');
    }

    public function runtimeException() : Response
    {
        throw new RuntimeException('A runtime exception');
    }
}
