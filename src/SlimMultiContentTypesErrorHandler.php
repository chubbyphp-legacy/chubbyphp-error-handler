<?php

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class SlimMultiContentTypesErrorHandler implements SlimErrorHandlerInterface
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
     * @param ContentTypeResolverInterface   $contentTypeResolver
     * @param ErrorResponseProviderInterface $fallbackProvider
     * @param array                          $providers
     */
    public function __construct(
        ContentTypeResolverInterface $contentTypeResolver,
        ErrorResponseProviderInterface $fallbackProvider,
        array $providers = []
    ) {
        $this->contentTypeResolver = $contentTypeResolver;
        $this->fallbackProvider = $fallbackProvider;
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
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
        $contentType = $this->contentTypeResolver->getContentType($request);

        if (isset($this->providers[$contentType])) {
            return $this->providers[$contentType]->get($request, $response, $exception);
        }

        return $this->fallbackProvider->get($request, $response, $exception);
    }
}
