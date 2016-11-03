<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler;

use Negotiation\Negotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class AdvancedErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $this->registerRequirements($container);

        $container['errorHandler.defaultProvider'] = function () use ($container) {
            throw new \RuntimeException('Please configure your default provider for error handler!');
        };

        $container['errorHandler.providers'] = function () use ($container) {
            return [];
        };

        $container['errorHandler.service'] = function () use ($container) {
            return new AdvancedErrorHandler(
                $container['errorHandler.contentTypeResolver'],
                $container['errorHandler.defaultProvider'],
                $container['errorHandler.providers'],
                $container['logger'] ?? null
            );
        };
    }

    /**
     * @param Container $container
     */
    private function registerRequirements(Container $container)
    {
        $container['errorHandler.acceptNegation'] = function () use ($container) {
            return new Negotiator();
        };

        $container['errorHandler.contentTypeResolver'] = function () use ($container) {
            return new ContentTypeResolver($container['errorHandler.acceptNegation']);
        };
    }
}
