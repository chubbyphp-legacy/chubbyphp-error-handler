<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ErrorHandlerMiddleware
{
    /**
     * @var ErrorHandlerInterface
     */
    private $errorHandler;

    /**
     * @param ErrorHandlerInterface $errorHandler
     */
    public function __construct(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            return $next($request, $response);
        } catch (\Exception $exception) {
            $errorHandler = $this->errorHandler;

            return $errorHandler($request, $response, $exception);
        }
    }
}
