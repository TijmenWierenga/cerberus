<?php

namespace Cerberus\PropertyAccess;

use Cerberus\Exception\PropertyAccessException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ObjectUpdater
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    public function __construct(PropertyAccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    public function update(object $object, iterable $values): object
    {
        foreach ($values as $key => $value) {
            if (! $this->accessor->isWritable($object, $key)) {
                throw PropertyAccessException::nonWritableProperty(get_class($object), $key);
            }

            $this->accessor->setValue($object, $key, $value);
        }

        return $object;
    }
}
