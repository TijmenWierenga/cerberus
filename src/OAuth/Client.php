<?php

namespace Cerberus\OAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class Client implements ClientEntityInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string[]
     */
    private $redirectUri;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var string[]
     */
    private $allowedGrantTypes;

    /**
     * Client constructor.
     * @param UuidInterface $id
     * @param string $name
     * @param string $clientSecret
     * @param string[] $redirectUri
     */
    private function __construct(
        UuidInterface $id, string $name, string $clientSecret, array $redirectUri, array $allowedGrantTypes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->clientSecret = $clientSecret;
        $this->allowedGrantTypes = $allowedGrantTypes;
    }

    public static function new(
        UuidInterface $id,
        string $name,
        string $clientSecret,
        array $redirectUri,
        array $allowedGrantTypes = ['auth_code', 'implicit', 'refresh_token']): self
    {
        return new self($id, $name, $clientSecret, $redirectUri, $allowedGrantTypes);
    }

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string[]
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function validateSecret(?string $against): bool
    {
        return $this->clientSecret === $against;
    }

    public function allowsGrantType(string $grantType, ...$grantTypes): bool
    {
        $grantTypes[] = $grantType;

        return ! array_diff($grantTypes, $this->allowedGrantTypes);
    }
}
