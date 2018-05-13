<?php

namespace Cerberus\OAuth\Service\Client;

use Cerberus\OAuth\Client;

class CreateClientResponse
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $clientSecret;

    public function __construct(Client $client, string $clientSecret)
    {
        $this->client = $client;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
