<?php

namespace Cerberus\PropertyAccess;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

final class PropertyInfoExtractorFactory
{
    public static function create(ObjectManager $objectManager): PropertyInfoExtractorInterface
    {
        $reflectionExtractor = new ReflectionExtractor();
        $doctrineExtractor = new DoctrineExtractor($objectManager->getMetadataFactory());

        return new PropertyInfoExtractor(
            [$reflectionExtractor, $doctrineExtractor],
            [$doctrineExtractor, $reflectionExtractor]
        );
    }
}
