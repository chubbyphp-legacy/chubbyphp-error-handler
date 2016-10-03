<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ContentTypeResolverInterface;
use Chubbyphp\ErrorHandler\ErrorHandler;
use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\ErrorHandler
 */
final class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeWithASupportedResponseProvider()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $errorHandler = new ErrorHandler(
            $this->getContentTypeResolver('application/xml'),
            'text/html',
            [
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
                $this->getErrorResponseProvider('text/html'),
            ]
        );

        self::assertSame(
            $response, $errorHandler($request, $response, HttpException::create($request, $response, 404, 'Not found'))
        );
    }

    public function testInvokeWithoutASupportedResponseProvider()
    {
        $response = $this->getResponse();

        $errorHandler = new ErrorHandler(
            $this->getContentTypeResolver('application/unknown'),
            'text/html',
            [
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
                $this->getErrorResponseProvider('text/html'),
            ]
        );

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception()));
    }

    public function testInvokeWithoutADefaultResponseProvider()
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('Default provider is missing!');

        $errorHandler = new ErrorHandler(
            $this->getContentTypeResolver('application/xml'),
            'text/html',
            []
        );

        $errorHandler($this->getRequest(), $this->getResponse(), new \Exception());
    }

    /**
     * @param string $contentType
     *
     * @return ContentTypeResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getContentTypeResolver(string $contentType): ContentTypeResolverInterface
    {
        $resolver = $this
            ->getMockBuilder(ContentTypeResolverInterface::class)
            ->setMethods(['getContentType'])
            ->getMockForAbstractClass()
        ;

        $resolver
            ->expects(self::any())
            ->method('getContentType')
            ->willReturnCallback(function (Request $request) use ($contentType) {
                return $contentType;
            })
        ;

        return $resolver;
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
