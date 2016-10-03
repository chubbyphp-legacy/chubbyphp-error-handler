<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\HttpException
 */
final class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $status = 404;
        $message = 'Can\'t find the wished page';

        $exception = HttpException::create($request, $response, $status, $message);

        self::assertSame($request, $exception->getRequest());
        self::assertSame($response, $exception->getResponse());
        self::assertSame($status, $exception->getCode());
        self::assertSame($message, $exception->getMessage());
    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequest(): Request
    {
        return $this->getMockBuilder(Request::class)->setMethods([])->getMockForAbstractClass();
    }

    /**
     * @return Response|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponse(): Response
    {
        return $this->getMockBuilder(Response::class)->setMethods([])->getMockForAbstractClass();
    }
}
