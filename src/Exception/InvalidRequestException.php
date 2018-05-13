<?php

namespace Cerberus\Exception;

use RuntimeException;

class InvalidRequestException extends RuntimeException
{
    public static function invalidJson(): self
    {
        return new self("The Request body contains invalid JSON");
    }
}
