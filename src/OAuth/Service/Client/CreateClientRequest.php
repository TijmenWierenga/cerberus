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
     * CreateClientRequest constructor.
     */
    public function __construct(string $name, string ...$redirectUris)
    {
        $this->name = $name;
        $this->redirectUris = $redirectUris;
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
}
