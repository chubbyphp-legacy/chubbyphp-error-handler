<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler\Slim;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SimpleErrorHandlerProvider implements ServiceProviderInterface
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
            return new SimpleErrorHandler($container['errorHandler.defaultProvider']);
        };
    }
}
