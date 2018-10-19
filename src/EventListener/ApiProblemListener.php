<?php

declare(strict_types=1);

namespace Libero\ApiProblemBundle\EventListener;

use FluentDOM\DOM\Element;
use Libero\ApiProblemBundle\Exception\ApiProblem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use function strtolower;
use function substr;

final class ApiProblemListener
{
    public function onKernelException(GetResponseForExceptionEvent $event) : void
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if (!$exception instanceof ApiProblem) {
            $exception = new ApiProblem($request, $exception);
        }

        $document = $exception->getDocument();
        /** @var Element $root */
        $root = $document->documentElement;

        $language = $root->getAttribute('xml:lang') ?? 'en';

        if (!$root('count(rfc7807:status)')) {
            $previous = $exception->getPrevious();
            if ($previous instanceof HttpExceptionInterface) {
                $status = $previous->getStatusCode();
            } else {
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            $root->appendElement('status', (string) $status);
        } else {
            $status = (int) $root('rfc7807:status[1]')[0]->nodeValue;
        }

        if (!$root('count(rfc7807:title)')) {
            $title = $root->appendElement('title', Response::$statusTexts[$status]);
            if ('en' !== substr($language, 0, 2)) {
                $title->setAttribute('xml:lang', 'en');
            }
        }

        $response = new Response(
            $exception->getDocument()->C14N(),
            $status,
            [
                'Content-Language' => $language,
                'Content-Type' => 'application/problem+xml; charset='.strtolower($document->encoding),
            ]
        );

        $event->setException($exception);
        $event->setResponse($response);
    }
}
