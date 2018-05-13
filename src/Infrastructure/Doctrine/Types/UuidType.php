<?php

namespace Cerberus\Infrastructure\Doctrine\Types;

use Doctrine\ODM\MongoDB\Types\Type;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidType extends Type
{
    /**
     * @param UuidInterface $id
     * @return string
     */
    public function convertToDatabaseValue($id): string
    {
        return (string) $id;
    }

    public function convertToPHPValue($id): UuidInterface
    {
        return Uuid::fromString($id);
    }
}
