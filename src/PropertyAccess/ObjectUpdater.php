<?php

namespace Cerberus\PropertyAccess;

use Cerberus\Exception\PropertyAccessException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

final class ObjectUpdater implements ObjectUpdaterInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;
    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfo;
    /**
     * @var DocumentManager
     */
    private $documentManager;

    public function __construct(
        PropertyAccessorInterface $accessor,
        PropertyInfoExtractorInterface $propertyInfo,
        DocumentManager $documentManager
    ) {
        $this->accessor = $accessor;
        $this->propertyInfo = $propertyInfo;
        $this->documentManager = $documentManager;
    }

    public function update(object $object, iterable $values): object
    {
        foreach ($values as $key => $value) {
            if (! $this->accessor->isWritable($object, $key)) {
                throw PropertyAccessException::nonWritableProperty(get_class($object), $key);
            }

            $types = $this->propertyInfo->getTypes(get_class($object), $key);

            $result = array_reduce($types, function (array $result, Type $type) {
                if ($result['collection'] === true) {
                    return $result;
                }

                return [
                    'collection' => $type->isCollection(),
                    'type' => $type->isCollection() ? $type->getCollectionValueType() : null
                ];
            }, [
                'collection' => false,
                'type' => null
            ]);

            if ($result['collection'] === true && $result['type']->getBuiltInType() === 'object') {
                foreach ($value as $index => $item) {
                    // TODO: Instead of getting a reference, create a RepositoryCollection with lazy loading.
                    // Fetch the repository and findManyById
                    // Then replace the value array with the actual entity.
                    // Repository will throw NotFoundException when entity does not exist
                    // Catch the exception and throw a 400 exception from here
                    $value[$index] = $this->documentManager->getReference($result['type']->getClassName(), $item);
                }
            }

            $this->accessor->setValue($object, $key, $value);
        }

        return $object;
    }
}
