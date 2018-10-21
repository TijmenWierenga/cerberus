<?php

namespace Cerberus\OAuth\Service\User;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;

class UpdateUserRequest
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var iterable
     */
    private $values;

    public function __construct(string $id, iterable $values)
    {
        $this->id = $id;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return iterable
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    public static function withReferences(ObjectManager $objectManager): self
    {

    }
}
