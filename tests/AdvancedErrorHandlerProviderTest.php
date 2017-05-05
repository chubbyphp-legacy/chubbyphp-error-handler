<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\AdvancedErrorHandlerProvider;
use Chubbyphp\ErrorHandler\ErrorHandlerMiddleware;
use Chubbyphp\Tests\ErrorHandler\Resources\ErrorResponseProvider\JsonErrorResponseProvider;
use Chubbyphp\Tests\ErrorHandler\Resources\ErrorResponseProvider\XmlErrorResponseProvider;
use Pimple\Container;

/**
 * @covers \Chubbyphp\ErrorHandler\AdvancedErrorHandlerProvider
 */
final class AdvancedErrorHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutDefaultProviderExpectsException()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Please configure your default provider for error handler!');

        $container = new Container();
        $container->register(new AdvancedErrorHandlerProvider());

        $container['errorHandler.middleware'];
    }

    public function testWithDefaultProvider()
    {
        $container = new Container();
        $container->register(new AdvancedErrorHandlerProvider());

        $container['errorHandler.defaultProvider'] = function () use ($container) {
            return new JsonErrorResponseProvider();
        };

        self::assertInstanceOf(ErrorHandlerMiddleware::class, $container['errorHandler.middleware']);
    }

    public function testWithProviders()
    {
        $container = new Container();
        $container->register(new AdvancedErrorHandlerProvider());

        $container['errorHandler.defaultProvider'] = function () use ($container) {
            return new JsonErrorResponseProvider();
        };

        $container->extend('errorHandler.providers', function (array $providers) {
            $providers[] = new XmlErrorResponseProvider();

            return $providers;
        });

        self::assertInstanceOf(ErrorHandlerMiddleware::class, $container['errorHandler.middleware']);
    }
}
