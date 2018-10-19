<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional\App;

use Exception;
use FluentDOM\DOM\Element;
use Libero\ApiProblemBundle\Exception\ApiProblem;
use Symfony\Component\HttpFoundation\Request;
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

    public function apiProblem(Request $request) : Response
    {
        $apiProblem = new ApiProblem($request);
        /** @var Element $root */
        $root = $apiProblem->getDocument()->documentElement;

        $root->appendElement('status', (string) Response::HTTP_TOO_MANY_REQUESTS);
        $root->appendElement('title', 'Too Many Requests');
        $root->appendElement('details', 'Calm it down.');

        throw $apiProblem;
    }
}
