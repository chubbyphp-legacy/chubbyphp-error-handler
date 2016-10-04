<?php

namespace Chubbyphp\ErrorHandler\Slim;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface ErrorHandlerInterface
{
    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception): Response;
}
