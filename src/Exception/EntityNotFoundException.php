<?php

namespace Cerberus\Exception;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    public static function forClass(string $class, string $id): self
    {
        return new self("Entity {$class}:{$id} could not be found");
    }
}
