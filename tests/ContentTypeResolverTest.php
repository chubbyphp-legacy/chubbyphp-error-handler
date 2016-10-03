<?php

namespace Chubbyphp\Tests\ErrorHandler;

use Chubbyphp\ErrorHandler\ContentTypeResolver;
use Negotiation\BaseAccept;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers Chubbyphp\ErrorHandler\ContentTypeResolver
 */
final class ContentTypeResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testWithMatch()
    {
        $request = $this->getRequest(
            ['Accept' => ['text/html', 'application/xhtml+xml', 'application/xml;q=0.9', '*/*;q=0.8']]
        );

        $resolver = new ContentTypeResolver(
            $this->getNegotiator($this->getBest('application/xml')),
            ['application/json', 'application/xml', 'text/html']
        );

        self::assertSame('application/xml', $resolver->getContentType($request));
    }

    public function testWithoutMatch()
    {
        $request = $this->getRequest(
            ['Accept' => ['text/html', 'application/xhtml+xml', 'application/xml;q=0.9', '*/*;q=0.8']]
        );

        $resolver = new ContentTypeResolver(
            $this->getNegotiator(),
            ['application/json', 'application/xml', 'text/html']
        );

        self::assertNull($resolver->getContentType($request));
    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequest(array $headers): Request
    {
        $request = $this->getMockBuilder(Request::class)->setMethods(['getHeaderLine'])->getMockForAbstractClass();

        $request->__headers = $headers;
        $request
            ->expects(self::any())
            ->method('getHeaderLine')
            ->willReturnCallback(function (string $name) use ($request) {
                if (!isset($request->__headers[$name])) {
                    return '';
                }

                return implode(', ', $request->__headers[$name]);
            })
        ;

        return $request;
    }

    /**
     * @param BaseAccept|null $accept
     *
     * @return Negotiator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getNegotiator(BaseAccept $accept = null): Negotiator
    {
        $negotiator = $this->getMockBuilder(Negotiator::class)->setMethods(['getBest'])->getMock();

        $negotiator
            ->expects(self::any())
            ->method('getBest')
            ->willReturnCallback(function ($header, array $priorities) use ($accept) {
                return $accept;
            })
        ;

        return $negotiator;
    }

    /**
     * @param string $value
     *
     * @return BaseAccept|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getBest(string $value): BaseAccept
    {
        $accept = $this
            ->getMockBuilder(BaseAccept::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMockForAbstractClass()
        ;

        $accept
            ->expects(self::any())
            ->method('getValue')
            ->willReturnCallback(function () use ($value) {
                return $value;
            })
        ;

        return $accept;
    }
}
