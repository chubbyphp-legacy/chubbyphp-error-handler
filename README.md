# chubbyphp-error-handler

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-error-handler.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-error-handler)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-error-handler/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-error-handler)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-error-handler/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-error-handler)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-error-handler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-error-handler/?branch=master)

## Description

A simple Error Handler Interface for PSR7.

## Requirements

 * php: ~7.0
 * psr/http-message: ~1.0

## Suggest

 * pimple/pimple: ~3.0
 * willdurand/negotiation: ~2.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-error-handler][1].

## Usage

### SimpleErrorHandler / AdvancedErrorHandler

#### JsonErrorResponseProvider (implements ErrorHandlerProvider)

```{.php}
<?php

namespace MyProject\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorHandlerProvider;

class JsonErrorResponseProvider implements ErrorHandlerProvider
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception $exception
     * @return Response
     */
    public function get(Request $request, Response $response, \Exception $exception): Response
    {
        $response->getBody()->write(json_encode([
            'exception' => ['message' => $exception->getMessage(), 'code' => $exception->getCode()]])
        );

        return $response;
    }
}
```

### ErrorHandlerMiddleware

```{.php}
<?php

use Chubbyphp\ErrorHandler\ErrorHandlerMiddleware;
use Chubbyphp\ErrorHandler\ErrorHandlerInterface;

$middleware = new ErrorHandlerMiddleware(new <ErrorHandlerInterface>);
$middleware($request, $response, $next);
```

### SimpleErrorHandler

#### SimpleErrorHandler

```{.php}
<?php

use Chubbyphp\ErrorHandler\SimpleErrorHandler;

$errorHandler = new SimpleErrorHandler($provider);

$response = $errorHandler($request, $response, $exception);
```

#### SimpleErrorHandlerProvider (Pimple)

```{.php}
<?php

use Chubbyphp\ErrorHandler\SimpleErrorHandlerProvider;
use MyProject\ErrorHandler\JsonErrorResponseProvider;
use Pimple/Container;

$container = new Container();
$container->register(new SimpleErrorHandlerProvider);

// IMPORTANT: without this definition, the error handler will not work!
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return new JsonErrorResponseProvider;
};

$app->add($container['errorHandler.middleware']);
```

### AdvancedErrorHandler

#### ContentTypeResolver (needed only for multi content type error handler)

```{.php}
<?php

use Chubbyphp\ErrorHandler\ContentTypeResolver;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface as Request;

$resolver = new ContentTypeResolver(new Negotiator);
$resolver->getContentType($request, ['text/html']);
```

#### AdvancedErrorHandler

```{.php}
<?php

use Chubbyphp\ErrorHandler\AdvancedErrorHandler;

$errorHandler = new AdvancedErrorHandler($resolver, $fallbackProvider, $providers);

$response = $errorHandler($request, $response, $expection);
```

#### AdvancedErrorHandlerProvider (Pimple)

```{.php}
<?php

use Chubbyphp\ErrorHandler\AdvancedErrorHandlerProvider;
use MyProject\ErrorHandler\JsonErrorResponseProvider;
use MyProject\ErrorHandler\XmlErrorResponseProvider;
use Pimple/Container;

$container = new Container();
$container->register(new AdvancedErrorHandlerProvider);

// IMPORTANT: without this definition, the error handler will not work!
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return new JsonErrorResponseProvider;
};

// optional: add more than the default provider
$container->extend('errorHandler.providers', function (array $providers) {
    $providers[] = new XmlErrorResponseProvider;

    return $providers;
});

$app->add($container['errorHandler.middleware']);
```

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-error-handler

## Copyright

Dominik Zogg 2016
