<?php

namespace Cerberus\Exception;

class EntityNotFoundException extends \RuntimeException
{
    public static function create(string $class, string $id): self
    {
        return new self("Entity {$class}:{$id} could not be found");
    }
}
