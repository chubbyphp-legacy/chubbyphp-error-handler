<?php

namespace Chubbyphp\Tests\ErrorHandler\Slim;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\Slim\SimpleErrorHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\Slim\SimpleErrorHandler
 */
final class SimpleErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $response = $this->getResponse();

        $errorHandler = new SimpleErrorHandler($this->getErrorResponseProvider('text/html'));

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception()));
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