<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ContentTypeResolverInterface;
use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\ErrorHandler\AdvancedErrorHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers Chubbyphp\ErrorHandler\AdvancedErrorHandler
 */
final class AdvancedErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    use LoggerTestTrait;

    public function testInvokeWithASupportedResponseProvider()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $logger = $this->getLogger();

        $errorHandler = new AdvancedErrorHandler(
            $this->getContentTypeResolver('application/xml'),
            $this->getErrorResponseProvider('text/html'),
            [
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
            ],
            $logger
        );

        self::assertSame(
            $response,
            $errorHandler($request, $response, HttpException::create($request, $response, 404, 'not found'))
        );

        self::assertCount(1, $logger->__logs);
        self::assertSame('warning', $logger->__logs[0]['level']);
        self::assertSame('error-handler: {code} {message}', $logger->__logs[0]['message']);
        self::assertSame(['status' => 404, 'message' => 'not found'], $logger->__logs[0]['context']);
    }

    public function testInvokeWithoutASupportedResponseProvider()
    {
        $response = $this->getResponse();

        $logger = $this->getLogger();

        $errorHandler = new AdvancedErrorHandler(
            $this->getContentTypeResolver('application/unknown'),
            $this->getErrorResponseProvider('text/html'),
            [
                $this->getErrorResponseProvider('application/json'),
                $this->getErrorResponseProvider('application/xml'),
            ],
            $logger
        );

        self::assertSame($response, $errorHandler($this->getRequest(), $response, new \Exception('error')));

        self::assertCount(1, $logger->__logs);
        self::assertSame('error', $logger->__logs[0]['level']);
        self::assertSame('error-handler: {code} {message}', $logger->__logs[0]['message']);
        self::assertSame(['status' => 500, 'message' => 'error'], $logger->__logs[0]['context']);
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
            ->willReturnCallback(function (Request $request, array $supportedContentTypes) use ($contentType) {
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
