<?php

namespace Chubbyphp\Tests\ErrorHandler\Resources\ErrorResponseProvider;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class XmlErrorResponseProvider implements ErrorResponseProviderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/xml';
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
        $response->getBody()->write(sprintf(
            '<exception><message>%s</message><code>%d</code></exception>',
            $exception->getMessage(),
            $exception->getCode())
        );

        return $response;
    }
}
