<?php

declare(strict_types=1);

namespace Libero\ApiProblemBundle\Exception;

use FluentDOM\DOM\Document;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class ApiProblem extends RuntimeException
{
    private $document;

    public function __construct(Request $request, ?Throwable $previous = null)
    {
        parent::__construct($previous ? $previous->getMessage() : '', $previous ? $previous->getCode() : 0, $previous);

        $this->document = new Document();

        $this->document->registerNamespace('', 'urn:ietf:rfc:7807');
        $this->document->xpath()->registerNamespace('rfc7807', 'urn:ietf:rfc:7807');

        $this->document->appendElement('problem', '', ['xml:lang' => $request->getLocale()]);
    }

    public function getDocument() : Document
    {
        return $this->document;
    }
}
