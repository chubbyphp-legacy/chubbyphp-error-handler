<?php

namespace Chubbyphp\Tests\ErrorHandler\Resources\ErrorResponseProvider;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class JsonErrorResponseProvider implements ErrorResponseProviderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
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
        $response->getBody()->write(json_encode([
            'exception' => ['message' => $exception->getMessage(), 'code' => $exception->getCode()], ])
        );

        return $response;
    }
}
