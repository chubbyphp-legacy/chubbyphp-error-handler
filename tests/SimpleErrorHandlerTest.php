<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\ErrorHandler\SimpleErrorHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\SimpleErrorHandler
 */
final class SimpleErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    use LoggerTestTrait;

    public function testInvokeWithHttpException()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $logger = $this->getLogger();

        $errorHandler = new SimpleErrorHandler($this->getErrorResponseProvider('text/html'), $logger);

        self::assertSame(
            $response,
            $errorHandler($request, $response, HttpException::create($request, $response, 404, 'not found'))
        );

        self::assertCount(1, $logger->__logs);
        self::assertSame('warning', $logger->__logs[0]['level']);
        self::assertSame('error-handler: {code} {message}', $logger->__logs[0]['message']);
        self::assertSame(['status' => 404, 'message' => 'not found'], $logger->__logs[0]['context']);
    }

    public function testInvokeWithException()
    {
        $response = $this->getResponse();

        $logger = $this->getLogger();

        $errorHandler = new SimpleErrorHandler($this->getErrorResponseProvider('text/html'), $logger);

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception('error')));

        self::assertCount(1, $logger->__logs);
        self::assertSame('error', $logger->__logs[0]['level']);
        self::assertSame('error-handler: {code} {message}', $logger->__logs[0]['message']);
        self::assertSame(['status' => 500, 'message' => 'error'], $logger->__logs[0]['context']);
    }

    /**
     * @param string $contentType
     *
     * @return ErrorResponseProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getErrorResponseProvider(string $contentType): ErrorResponseProviderInterface
    {
        $errorResponseProvider = $this
            ->getMockBuilder(ErrorResponseProviderInterface::class)
            ->setMethods(['getContentType', 'get'])
            ->getMockForAbstractClass()
        ;

        $errorResponseProvider
            ->expects(self::any())
            ->method('getContentType')
            ->willReturn($contentType)
        ;

        $errorResponseProvider
            ->expects(self::any())
            ->method('get')
            ->willReturnCallback(
                function (Request $request, Response $response, \Exception $exception) {
                    return $response;
                }
            )
        ;

        return $errorResponseProvider;
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
