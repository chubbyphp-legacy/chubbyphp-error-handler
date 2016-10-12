<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler\Slim;

use Chubbyphp\ErrorHandler\ContentTypeResolverInterface;
use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class AdvancedErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var ContentTypeResolverInterface
     */
    private $contentTypeResolver;

    /**
     * @var ErrorResponseProviderInterface
     */
    private $fallbackProvider;

    /**
     * @var ErrorResponseProviderInterface[]
     */
    private $providers = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ContentTypeResolverInterface   $contentTypeResolver
     * @param ErrorResponseProviderInterface $fallbackProvider
     * @param array                          $providers
     * @param LoggerInterface|null           $logger
     */
    public function __construct(
        ContentTypeResolverInterface $contentTypeResolver,
        ErrorResponseProviderInterface $fallbackProvider,
        array $providers = [],
        LoggerInterface $logger = null
    ) {
        $this->contentTypeResolver = $contentTypeResolver;
        $this->fallbackProvider = $fallbackProvider;
        $this->addProvider($fallbackProvider);
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param ErrorResponseProviderInterface $provider
     */
    private function addProvider(ErrorResponseProviderInterface $provider)
    {
        $this->providers[$provider->getContentType()] = $provider;
    }

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     *
     * @throws \LogicException
     */
    public function __invoke(Request $request, Response $response, \Exception $exception): Response
    {
        $contentType = $this->contentTypeResolver->getContentType($request, array_keys($this->providers));

        $this->logException($exception);

        if (isset($this->providers[$contentType])) {
            return $this->providers[$contentType]->get($request, $response, $exception);
        }

        return $this->fallbackProvider->get($request, $response, $exception);
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
