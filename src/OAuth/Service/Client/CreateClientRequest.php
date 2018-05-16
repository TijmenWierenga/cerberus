<?php

namespace Cerberus\OAuth\Service\Client;

class CreateClientRequest
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string[]
     */
    private $redirectUris;
    /**
     * @var string[]
     */
    private $allowedGrantTypes;

    /**
     * CreateClientRequest constructor.
     */
    public function __construct(string $name, array $redirectUris, array $allowedGrantTypes = [])
    {
        $this->name = $name;
        $this->redirectUris = $redirectUris;
        $this->allowedGrantTypes = $allowedGrantTypes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getRedirectUris(): array
    {
        return $this->redirectUris;
    }

    /**
     * @return string[]
     */
    public function getAllowedGrantTypes(): array
    {
        return $this->allowedGrantTypes;
    }
}
