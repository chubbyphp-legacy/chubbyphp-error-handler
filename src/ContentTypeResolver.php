<?php

declare(strict_types=1);

namespace Chubbyphp\ErrorHandler;

use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ContentTypeResolver implements ContentTypeResolverInterface
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    /**
     * @param Negotiator $negotiator
     */
    public function __construct(Negotiator $negotiator)
    {
        $this->negotiator = $negotiator;
    }

    /**
     * @param Request $request
     * @param array   $supportedContentTypes
     *
     * @return string|null
     */
    public function getContentType(Request $request, array $supportedContentTypes)
    {
        /** @var Accept $contentType */
        $contentType = $this->negotiator->getBest($request->getHeaderLine('Accept'), $supportedContentTypes);
        if (null !== $contentType) {
            return $contentType->getValue();
        }

        return null;
    }
}
