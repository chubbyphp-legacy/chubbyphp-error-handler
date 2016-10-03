<?php

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ErrorHandler
{
    /**
     * @var ContentTypeResolverInterface
     */
    private $contentTypeResolver;

    /**
     * @var string
     */
    private $defaultProvider;

    /**
     * @var ErrorResponseProviderInterface[]
     */
    private $providers = [];

    /**
     * @param ContentTypeResolverInterface     $contentTypeResolver
     * @param string                           $defaultProvider
     * @param ErrorResponseProviderInterface[] $providers
     */
    public function __construct(ContentTypeResolverInterface $contentTypeResolver, $defaultProvider, array $providers)
    {
        $this->contentTypeResolver = $contentTypeResolver;
        $this->defaultProvider = $defaultProvider;
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

        if (isset($this->providers[$this->defaultProvider])) {
            return $this->providers[$this->defaultProvider]->get($request, $response, $exception);
        }

        throw new \LogicException('Default provider is missing!');
    }
}
