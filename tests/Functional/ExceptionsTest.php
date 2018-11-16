<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional;

use Exception;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use function array_slice;

final class ExceptionsTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function it_handles_an_exception() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/500');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('en', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="en" xmlns="urn:ietf:rfc:7807">
                <status>500</status>
                <title>Internal Server Error</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_logs_critical_exceptions() : void
    {
        $kernel = static::getKernel();
        /** @var BufferingLogger $logger */
        $logger = $this->getContainer()->get('logger');

        $request = Request::create('/500');

        $kernel->handle($request);

        $this->assertEquals(
            [
                [
                    'critical',
                    'Exception '.Exception::class.': "An exception" at '.__DIR__.'/App/Controller.php line 26',
                    ['exception' => new Exception('An exception')],
                ],
            ],
            array_slice($logger->cleanLogs(), 1)
        );
    }

    /**
     * @test
     */
    public function it_logs_exceptions() : void
    {
        $kernel = static::getKernel();
        /** @var BufferingLogger $logger */
        $logger = $this->getContainer()->get('logger');

        $request = Request::create('/418');

        $kernel->handle($request);

        $this->assertEquals(
            [
                [
                    'error',
                    'Exception '.HttpException::class.': "An HTTP exception" at '.__DIR__.'/App/Controller.php line 21',
                    ['exception' => new HttpException(418, 'An HTTP exception')],
                ],
            ],
            array_slice($logger->cleanLogs(), 1)
        );
    }

    /**
     * @test
     */
    public function it_handles_an_exception_for_a_request_wanting_a_different_language() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/500');
        $request->setLocale('es');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('es', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="es" xmlns="urn:ietf:rfc:7807">
                <status>500</status>
                <title xml:lang="en">Internal Server Error</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_handles_an_exception_for_a_request_wanting_a_type_of_english() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/500');
        $request->setLocale('en-GB-scouse');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('en-GB-scouse', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="en-GB-scouse" xmlns="urn:ietf:rfc:7807">
                <status>500</status>
                <title>Internal Server Error</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_handles_a_http_exception() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/418');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('en', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="en" xmlns="urn:ietf:rfc:7807">
                <status>418</status>
                <title>I\'m a teapot</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_handles_a_http_exception_for_a_request_wanting_a_different_language() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/418');
        $request->setLocale('es');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('es', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="es" xmlns="urn:ietf:rfc:7807">
                <status>418</status>
                <title xml:lang="en">I\'m a teapot</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_handles_a_http_exception_for_a_request_wanting_a_type_of_english() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/418');
        $request->setLocale('en-GB-scouse');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('en-GB-scouse', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="en-GB-scouse" xmlns="urn:ietf:rfc:7807">
                <status>418</status>
                <title>I\'m a teapot</title>
            </problem>',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_allows_the_api_problem_to_be_extended() : void
    {
        $kernel = static::getKernel();

        $request = Request::create('/500-runtime');

        $response = $kernel->handle($request);

        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('en', $response->headers->get('Content-Language'));
        $this->assertXmlStringEqualsXmlString(
            '<problem xml:lang="en" xmlns="urn:ietf:rfc:7807">
                <status>429</status>
                <title>A custom title: A runtime exception</title>
            </problem>',
            $response->getContent()
        );
    }
}
