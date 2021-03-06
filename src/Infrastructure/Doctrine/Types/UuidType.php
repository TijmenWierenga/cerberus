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

    public function closureToPHP()
    {
        // Return the string body of a PHP closure that will receive $value
        // and store the result of a conversion in a $return variable
        return '$return = \Ramsey\Uuid\Uuid::fromString($value);';
    }
}
