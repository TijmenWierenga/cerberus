<?php

namespace Cerberus\PropertyAccess;

use Cerberus\Exception\PropertyAccessException;

interface ObjectUpdaterInterface
{
    /**
     * @param object $object
     * @param iterable $values
     * @return object  The updated object
     * @throws PropertyAccessException
     */
    public function update(object $object, iterable $values): object;
}
