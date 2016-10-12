<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler\Slim;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class SimpleErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var ErrorResponseProviderInterface
     */
    private $provider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ErrorResponseProviderInterface $provider
     */
    public function __construct(ErrorResponseProviderInterface $provider, LoggerInterface $logger = null)
    {
        $this->provider = $provider;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception): Response
    {
        $this->logException($exception);

        return $this->provider->get($request, $response, $exception);
    }

    /**
     * @param \Exception $exception
     */
    private function logException(\Exception $exception)
    {
        if ($exception instanceof HttpException) {
            $this->logger->warning(
                'error-handler: {code} {message}',
                ['status' => $exception->getCode(), 'message' => $exception->getMessage()]
            );

            return;
        }

        $this->logger->error(
            'error-handler: {code} {message}',
            ['status' => 500, 'message' => $exception->getMessage()]
        );
    }
}
