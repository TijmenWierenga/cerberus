<?php

namespace Cerberus\OAuth;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
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
    private $redirectUris;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var string[]
     */
    private $allowedGrantTypes;
    /**
     * @var Collection
     */
    private $scopes;

    /**
     * Client constructor.
     * @param UuidInterface $id
     * @param string $name
     * @param string $clientSecret
     * @param string[] $redirectUris
     */
    private function __construct(
        UuidInterface $id,
        string $name,
        string $clientSecret,
        array $redirectUris,
        array $allowedGrantTypes
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->redirectUris = $redirectUris;
        $this->clientSecret = $clientSecret;
        $this->allowedGrantTypes = $allowedGrantTypes;
        $this->scopes = new ArrayCollection();
    }

    public static function new(
        UuidInterface $id,
        string $name,
        string $clientSecret,
        array $redirectUris,
        array $allowedGrantTypes = ['auth_code', 'implicit', 'refresh_token']
    ): self {
        return new self($id, $name, $clientSecret, $redirectUris, $allowedGrantTypes);
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

    public function setName(string $name): void
    {
        $this->name = $name;
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
        return $this->redirectUris;
    }

    /**
     * Proxy method in plural for Symfony Property Accessor to allow updating values from an array.
     */
    public function getRedirectUris(): array
    {
        return $this->getRedirectUri();
    }

    public function addRedirectUri(string $redirectUri): void
    {
        if (! in_array($redirectUri, $this->redirectUris)) {
            $this->redirectUris[] = $redirectUri;
        }
    }

    public function removeRedirectUri(string $redirectUri): void
    {
        if (in_array($redirectUri, $this->redirectUris)) {
            $key = array_search($redirectUri, $this->redirectUris);

            if (is_integer($key)) {
                unset($this->redirectUris[$key]);
            }
        }
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

    public function addAllowedGrantType(string $grantType, string ...$grantTypes): void
    {
        $grantTypes[] = $grantType;

        $this->allowedGrantTypes = array_merge($this->allowedGrantTypes, $grantTypes);
    }

    /**
     * @return string[]
     */
    public function getAllowedGrantTypes(): array
    {
        return $this->allowedGrantTypes;
    }

    public function removeAllowedGrantType(string $grantType, string ...$grantTypes): void
    {
        $grantTypes[] = $grantType;

        foreach ($grantTypes as $grantType) {
            $key = array_search($grantType, $this->allowedGrantTypes);
            if (is_integer($key)) {
                unset($this->allowedGrantTypes[$key]);
            }
        }
    }

    public function addScope(ScopeEntityInterface $scope): void
    {
        if (! $this->hasScope($scope)) {
            $this->scopes->add($scope);
        }
    }

    public function hasScope(ScopeEntityInterface $scope): bool
    {
        return $this->scopes->contains($scope);
    }

    public function removeScope(ScopeEntityInterface $scope): void
    {
        if ($this->hasScope($scope)) {
            $this->scopes->removeElement($scope);
        }
    }
}
