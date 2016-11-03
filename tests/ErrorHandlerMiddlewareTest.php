<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorHandlerInterface;
use Chubbyphp\ErrorHandler\ErrorHandlerMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\ErrorHandlerMiddleware
 */
final class ErrorHandlerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeWithoutException()
    {
        $middleware = new ErrorHandlerMiddleware($this->getErrorHandler(0));

        $middleware($this->getRequest(), $this->getResponse(), function (Request $request, Response $response) {
            return $response;
        });
    }

    public function testInvokeWithException()
    {
        $middleware = new ErrorHandlerMiddleware($this->getErrorHandler(1));

        $middleware($this->getRequest(), $this->getResponse(), function (Request $request, Response $response) {
            throw new \Exception();
        });
    }

    /**
     * @param int $invoke
     *
     * @return ErrorHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getErrorHandler(int $invoke): ErrorHandlerInterface
    {
        $errorHandler = $this
            ->getMockBuilder(ErrorHandlerInterface::class)
            ->setMethods(['__invoke'])
            ->getMockForAbstractClass()
        ;

        $errorHandler
            ->expects(self::exactly($invoke))
            ->method('__invoke')
            ->willReturnCallback(function (Request $request, Response $response, \Exception $exception) {
                return $response;
            })
        ;

        return $errorHandler;
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
