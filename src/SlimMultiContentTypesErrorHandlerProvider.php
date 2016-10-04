<?php

namespace Chubbyphp\ErrorHandler;

use Negotiation\Negotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SlimMultiContentTypesErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['errorHandler.acceptNegation'] = function () use ($container) {
            return new Negotiator();
        };

        $container['errorHandler.contentTypeResolver'] = function () use ($container) {
            return new ContentTypeResolver($container['errorHandler.acceptNegation']);
        };

        $container['errorHandler.defaultProvider'] = function () use ($container) {
            throw new \RuntimeException('Please configure your default provider for error handler!');
        };

        $container['errorHandler.providers'] = function () use ($container) {
            return [];
        };

        $container['errorHandler'] = function () use ($container) {
            return new SlimMultiContentTypesErrorHandler(
                $container['errorHandler.contentTypeResolver'],
                $container['errorHandler.defaultProvider'],
                $container['errorHandler.providers']
            );
        };
    }
}
