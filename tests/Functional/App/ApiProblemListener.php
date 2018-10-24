<?php

declare(strict_types=1);

namespace tests\Libero\ApiProblemBundle\Functional\App;

use FluentDOM\DOM\Element;
use Libero\ApiProblemBundle\Event\CreateApiProblem;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use function get_class;

final class ApiProblemListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['libero.api_problem.create' => 'onCreateApiProblem'];
    }

    public function onCreateApiProblem(CreateApiProblem $creator) : void
    {
        /** @var Element $root */
        $root = $creator->getDocument()->documentElement;
        $exception = $creator->getException();

        if (RuntimeException::class !== get_class($exception)) {
            return;
        }

        $root->appendElement('status', (string) Response::HTTP_TOO_MANY_REQUESTS);
        $root->appendElement('title', "A custom title: {$exception->getMessage()}");
    }
}
