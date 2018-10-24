<?php

declare(strict_types=1);

namespace Libero\ApiProblemBundle\Event;

use FluentDOM\DOM\Document;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class CreateApiProblem extends Event
{
    public const NAME = 'libero.api_problem.create';

    private $document;
    private $exception;

    public function __construct(Request $request, Throwable $exception)
    {
        $this->exception = $exception;
        $this->document = new Document();

        $this->document->registerNamespace('', 'urn:ietf:rfc:7807');
        $this->document->xpath()->registerNamespace('rfc7807', 'urn:ietf:rfc:7807');

        $this->document->appendElement('problem', '', ['xml:lang' => $request->getLocale()]);
    }

    public function getDocument() : Document
    {
        return $this->document;
    }

    public function getException() : Throwable
    {
        return $this->exception;
    }
}
