<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler;

use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ContentTypeResolver implements ContentTypeResolverInterface
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Negotiator $negotiator
     */
    public function __construct(Negotiator $negotiator, LoggerInterface $logger = null)
    {
        $this->negotiator = $negotiator;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param Request $request
     * @param array   $supportedContentTypes
     *
     * @return string|null
     */
    public function getContentType(Request $request, array $supportedContentTypes)
    {
        $acceptHeaderLine = $request->getHeaderLine('Accept');

        /** @var Accept $accept */
        $accept = $this->negotiator->getBest($acceptHeaderLine, $supportedContentTypes);
        if (null !== $accept) {
            $contentType = $accept->getValue();
            $this->logger->info(
                'error-handler: resolved content type {contentType} for accept header line {acceptHeaderLine}',
                ['contentType' => $contentType, 'acceptHeaderLine' => $acceptHeaderLine]
            );

            return $contentType;
        }

        $this->logger->warning(
            'error-handler: could not resolve content type for accept header line {acceptHeaderLine}',
            ['acceptHeaderLine' => $acceptHeaderLine]
        );

        return null;
    }
}
