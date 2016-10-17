<?php

declare(strict_types=1);

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

    const STATUS_400 = 'Bad Request';
    const STATUS_401 = 'Unauthorized';
    const STATUS_402 = 'Payment Required';
    const STATUS_403 = 'Forbidden';
    const STATUS_404 = 'Not Found';
    const STATUS_405 = 'Method Not Allowed';
    const STATUS_406 = 'Not Acceptable';
    const STATUS_407 = 'Proxy Authentication Required';
    const STATUS_408 = 'Request Time-out';
    const STATUS_409 = 'Conflict';
    const STATUS_410 = 'Gone';
    const STATUS_411 = 'Length Required';
    const STATUS_412 = 'Precondition Failed';
    const STATUS_413 = 'Request Entity Too Large';
    const STATUS_414 = 'Request-URL Too Long';
    const STATUS_415 = 'Unsupported Media Type';
    const STATUS_416 = 'Requested range not satisfiable';
    const STATUS_417 = 'Expectation Failed';
    const STATUS_418 = 'I’m a teapot';
    const STATUS_420 = 'Policy Not Fulfilled';
    const STATUS_421 = 'Misdirected Request';
    const STATUS_422 = 'Unprocessable Entity';
    const STATUS_423 = 'Locked';
    const STATUS_424 = 'Failed Dependency';
    const STATUS_425 = 'Unordered Collection';
    const STATUS_426 = 'Upgrade Required';
    const STATUS_428 = 'Precondition Required';
    const STATUS_429 = 'Too Many Requests';
    const STATUS_431 = 'Request Header Fields Too Large';
    const STATUS_451 = 'Unavailable For Legal Reasons';
    const STATUS_444 = 'No Response';
    const STATUS_449 = 'The request should be retried after doing the appropriate action';

    const STATUS_500 = 'Internal Server Error';
    const STATUS_501 = 'Not Implemented';
    const STATUS_502 = 'Bad Gateway';
    const STATUS_503 = 'Service Unavailable';
    const STATUS_504 = 'Gateway Time-out';
    const STATUS_505 = 'HTTP Version not supported';
    const STATUS_506 = 'Variant Also Negotiates';
    const STATUS_507 = 'Insufficient Storage';
    const STATUS_508 = 'Loop Detected';
    const STATUS_509 = 'Bandwidth Limit Exceeded';
    const STATUS_510 = 'Not Extended';
    const STATUS_511 = 'Network Authentication Required';

    /**
     * @var bool
     */
    private $hasDefaultMessage;

    /**
     * @param Request     $request
     * @param Response    $response
     * @param int         $status
     * @param string|null $message
     *
     * @return HttpException
     */
    public static function create(Request $request, Response $response, int $status, string $message = null): self
    {
        $exception = new self($message ?? self::getMessageByStatus($status), $status);

        $exception->request = $request;
        $exception->response = $response;
        $exception->hasDefaultMessage = null === $message;

        return $exception;
    }

    /**
     * @param int $status
     *
     * @return string
     */
    private static function getMessageByStatus(int $status): string
    {
        $statusConstantName = 'STATUS_'.$status;
        $reflection = new \ReflectionClass(self::class);
        if ($reflection->hasConstant($statusConstantName)) {
            return $reflection->getConstant($statusConstantName);
        }

        return 'unknown';
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

    /**
     * @return bool
     */
    public function hasDefaultMessage(): bool
    {
        return $this->hasDefaultMessage;
    }
}
