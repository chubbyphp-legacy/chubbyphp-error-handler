<?php

namespace Chubbyphp\ErrorHandler;

final class HttpException extends \RuntimeException
{
    /**
     * @param int    $status
     * @param string $message
     *
     * @return HttpException
     */
    public static function create(int $status, string $message): self
    {
        return new self($message, $status);
    }
}
