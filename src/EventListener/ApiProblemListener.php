<?php

declare(strict_types=1);

namespace Libero\ApiProblemBundle\EventListener;

use FluentDOM\DOM\Element;
use Libero\ApiProblemBundle\Event\CreateApiProblem;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use function get_class;
use function sprintf;
use function strtolower;
use function substr;

final class ApiProblemListener
{
    private $eventDispatcher;
    private $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) : void
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        $apiProblemEvent = new CreateApiProblem($request, $exception);

        $this->eventDispatcher->dispatch($apiProblemEvent::NAME, $apiProblemEvent);

        $document = $apiProblemEvent->getDocument();
        /** @var Element $root */
        $root = $document->documentElement;

        $language = $root->getAttribute('xml:lang') ?? 'en';

        if (!$root('count(rfc7807:status)')) {
            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
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
            $document->saveXML(),
            $status,
            [
                'Content-Language' => $language,
                'Content-Type' => 'application/problem+xml; charset='.strtolower($document->encoding),
            ]
        );

        $event->setResponse($response);

        $level = $status >= 500 ? LogLevel::CRITICAL : LogLevel::ERROR;
        $message = sprintf(
            'Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $this->logger->log($level, $message, ['exception' => $exception]);
    }
}
