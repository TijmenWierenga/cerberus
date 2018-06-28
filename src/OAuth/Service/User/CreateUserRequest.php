<?php

namespace Cerberus\OAuth\Service\User;

use Cerberus\OAuth\Scope;
use Ramsey\Uuid\UuidInterface;

class CreateUserRequest
{

    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string[]
     */
    private $scopes;

    public function __construct(string $username, string $password, array $scopes)
    {
        $this->username = $username;
        $this->password = $password;
        $this->scopes = $scopes;
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

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
