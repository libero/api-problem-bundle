<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional;

use Symfony\Component\HttpFoundation\Request;

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
