<?php

namespace Cerberus\OAuth\Service\User;

use Psr\Http\Message\ServerRequestInterface;

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

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $body = $request->getParsedBody();

        return new self($body["username"], $body["password"], $body["scopes"]);
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
