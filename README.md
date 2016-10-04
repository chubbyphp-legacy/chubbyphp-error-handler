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

 * willdurand/negotiation: ~2.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-error-handler][1].

## Usage

### SlimSingleContentType / SlimMultiContentTypesErrorHandler

#### HtmlErrorProvider (implements ErrorHandlerProvider)

```{.php}
<?php

namespace MyProject\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorHandlerProvider;

class HtmlErrorHandlerProvider implements ErrorHandlerProvider
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function get(Request $request, Response $response, \Exception $exception): Response
    {
        // do some stuff, fancy rendering

        return $response;
    }
}
```

### SlimSingleContentType

#### SlimSingleContentTypeErrorHandler

```{.php}
<?php

use Chubbyphp\ErrorHandler\SlimSingleContentTypeErrorHandler;

$errorHandler = new SlimSingleContentTypeErrorHandler($provider);

$response = $errorHandler($request, $response, $exception);
```

### SlimMultiContentTypes

#### ContentTypeResolver (needed only for multi content type error handler)

```{.php}
<?php

use Chubbyphp\ErrorHandler\ContentTypeResolver;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface as Request;

$resolver = new ContentTypeResolver(new Negotiator, ['text/html']);
$resolver->getContentType($request); // "Accept: application/xml, text/html" => (text/html)
```

#### SlimMultiContentTypesErrorHandler

```{.php}
<?php

use Chubbyphp\ErrorHandler\SlimMultiContentTypesErrorHandler;

$errorHandler = new SlimMultiContentTypesErrorHandler($resolver, $fallbackProvider, $providers);

$response = $errorHandler($request, $response, $expection);
```

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-error-handler

## Copyright

Dominik Zogg 2016
