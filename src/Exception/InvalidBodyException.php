<?php

declare(strict_types=1);

namespace Weblabel\HttpClient\Exception;

class InvalidBodyException extends RuntimeException
{
    public static function forInvalidBody(): self
    {
        return new self('Invalid request body');
    }
}
