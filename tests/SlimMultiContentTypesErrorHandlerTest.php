<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ContentTypeResolverInterface;
use Chubbyphp\ErrorHandler\SlimMultiContentTypesErrorHandler;
use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\SlimMultiContentTypesErrorHandler
 */
final class SlimMultiContentTypesErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeWithASupportedResponseProvider()
    {
        $response = $this->getResponse();

        $fallbackProvider = $this->getErrorResponseProvider('text/html');

        $errorHandler = new SlimMultiContentTypesErrorHandler(
            $this->getContentTypeResolver('application/xml'),
            $fallbackProvider,
            [
                $fallbackProvider,
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
            ]
        );

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception()));
    }

    public function testInvokeWithoutASupportedResponseProvider()
    {
        $response = $this->getResponse();

        $fallbackProvider = $this->getErrorResponseProvider('text/html');

        $errorHandler = new SlimMultiContentTypesErrorHandler(
            $this->getContentTypeResolver('application/unknown'),
            $fallbackProvider,
            [
                $fallbackProvider,
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
            ]
        );

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception()));
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
