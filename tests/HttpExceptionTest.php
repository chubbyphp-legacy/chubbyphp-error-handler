<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\HttpException;

/**
 * @covers Chubbyphp\ErrorHandler\HttpException
 */
final class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $status = 404;
        $message = 'Can\'t find the wished page';

        $exception = HttpException::create($status, $message);

        self::assertSame($status, $exception->getCode());
        self::assertSame($message, $exception->getMessage());
    }
}
