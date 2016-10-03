<?php

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HttpException extends \RuntimeException
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $status
     * @param string   $message
     *
     * @return HttpException
     */
    public static function create(Request $request, Response $response, int $status, string $message = ''): self
    {
        $exception = new self($message, $status);
        $exception->request = $request;
        $exception->response = $response;

        return $exception;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
