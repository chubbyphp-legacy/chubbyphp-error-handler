<?php

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface ErrorHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $statusCode
     * @return Response
     */
    public function error(Request $request, Response $response, int $statusCode): Response;
}
