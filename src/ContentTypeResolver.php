<?php

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
     * @var array
     */
    private $supportedContentTypes;

    /**
     * @param Negotiator $negotiator
     * @param array      $supportedContentTypes
     */
    public function __construct(Negotiator $negotiator, array $supportedContentTypes)
    {
        $this->negotiator = $negotiator;
        $this->supportedContentTypes = $supportedContentTypes;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function getContentType(Request $request)
    {
        /** @var Accept $contentType */
        $contentType = $this->negotiator->getBest($request->getHeaderLine('Accept'), $this->supportedContentTypes);
        if (null !== $contentType) {
            return $contentType->getValue();
        }

        return null;
    }
}
