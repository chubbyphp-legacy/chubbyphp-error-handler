<?php

namespace Chubbyphp\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;

interface ContentTypeResolverInterface
{
    /**
     * @param Request $request
     * @param array   $supportedContentTypes
     *
     * @return string|null
     */
    public function getContentType(Request $request, array $supportedContentTypes);
}
