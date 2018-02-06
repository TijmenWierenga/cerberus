<?php

namespace TijmenWierenga\Cerberus;

use League\OAuth2\Server\Entities\UserEntityInterface;
use Ramsey\Uuid\UuidInterface;

class User implements UserEntityInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * User constructor.
     * @param UuidInterface $id
     * @param string $username
     * @param string $password
     */
    private function __construct(UuidInterface $id, string $username, string $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public static function new(UuidInterface $id, string $username, string $password): self
    {
        return new self($id, $username, $password);
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return (string) $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
