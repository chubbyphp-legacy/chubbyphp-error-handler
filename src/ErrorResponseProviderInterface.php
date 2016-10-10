<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface ErrorResponseProviderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function get(Request $request, Response $response, \Exception $exception): Response;
}
