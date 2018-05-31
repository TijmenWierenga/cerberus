<?php

namespace Cerberus\Exception;

use RuntimeException;

class PropertyAccessException extends RuntimeException
{
    public static function nonWritableProperty(string $class, string $key): self
    {
        return new self("Property {$key} of class {$class} is not writable");
    }
}
