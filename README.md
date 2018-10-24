ApiProblemBundle
================

[![Build Status](https://travis-ci.com/libero/api-problem-bundle.svg?branch=master)](https://travis-ci.com/libero/api-problem-bundle)

This is a [Symfony](https://symfony.com/) bundle implements the Libero API error standard (based on [Problem Details for HTTP APIs](https://tools.ietf.org/html/rfc7807)).

Getting started
---------------

Using [Composer](https://getcomposer.org/) you can add the bundle as a dependency:

```
composer require libero/api-problem-bundle
```

If you're not using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html), you'll need to enable the bundle in your application.

All exceptions are caught and turned into a `application/problem+xml` response.

To modify the contents of the response, you can listen for the [`libero.api_problem.create` event](src/Event/CreateApiProblem.php).

Any remaining exceptions will be caught; unless the exception is an instance of Symfony's `HttpExceptionInterface`, the response will be `500 Internal Server Error`.

Getting help
------------

-  Report a bug or request a feature on [GitHub](https://github.com/libero/libero/issues/new/choose).
-  Ask a question on the [Libero Community Slack](https://libero-community.slack.com/).
