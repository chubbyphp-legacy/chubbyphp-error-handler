<?php

namespace Chubbyphp\ErrorHandler;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SlimSingleContentTypeErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['errorHandler.defaultProvider'] = function () use ($container) {
            throw new \RuntimeException('Please configure your default provider for error handler!');
        };

        $container['errorHandler'] = function () use ($container) {
            return new SlimSingleContentTypeErrorHandler($container['errorHandler.defaultProvider']);
        };
    }
}
