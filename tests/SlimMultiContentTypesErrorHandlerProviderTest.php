<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\SlimMultiContentTypesErrorHandlerProvider;
use Pimple\Container;

/**
 * @covers Chubbyphp\ErrorHandler\SlimMultiContentTypesErrorHandlerProvider
 */
final class SlimMultiContentTypesErrorHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutDefaultProviderExpectsException()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Please configure your default provider for error handler!');

        $container = new Container();
        $container->register(new SlimMultiContentTypesErrorHandlerProvider);

        $container['errorHandler'];
    }
}
