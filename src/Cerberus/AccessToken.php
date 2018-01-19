<?php

namespace TijmenWierenga\Cerberus;

use DateTime;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;

    /**
     * @var ClientEntityInterface
     */
    private $client;
    /**
     * @var DateTime
     */
    private $expiryDateTime;
    /**
     * @var UuidInterface
     */
    private $userIdentifier;
    /**
     * @var Collection|ScopeEntityInterface[]
     */
    private $scopes;
    /**
     * @var UuidInterface
     */
    private $identifier;

    /**
     * AccessToken constructor.
     * @param UuidInterface $identifier
     * @param UuidInterface $userIdentifier
     * @param ClientEntityInterface $client
     * @param Collection|ScopeEntityInterface[] $scopes
     * @param DateTime $expiryDateTime
     */
    private function __construct(
        UuidInterface $identifier,
        UuidInterface $userIdentifier,
        ClientEntityInterface $client,
        Collection $scopes,
        DateTime $expiryDateTime
    ) {
        $this->client = $client;
        $this->expiryDateTime = $expiryDateTime;
        $this->userIdentifier = $userIdentifier;
        $this->scopes = $scopes;
        $this->identifier = $identifier;
    }

    public static function new(ClientEntityInterface $client, Collection $scopes, UuidInterface $userId): self
    {
        return new self(
            Uuid::uuid4(),
            $userId,
            $client,
            $scopes,
            new DateTime() // @TODO: Add a TTL as an argument
        );
    }

    /**
     * @return ClientEntityInterface
     */
    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    /**
     * @return DateTime
     */
    public function getExpiryDateTime(): DateTime
    {
        return $this->expiryDateTime;
    }

    /**
     * @return string|int
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier->toString();
    }

    /**
     * @return Collection|ScopeEntityInterface[]
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier->toString();
    }

    /**
     * Set the token's identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = Uuid::fromString($identifier);
    }

    /**
     * Set the date time when the token expires.
     *
     * @param DateTime $dateTime
     */
    public function setExpiryDateTime(DateTime $dateTime): void
    {
        $this->expiryDateTime = $dateTime;
    }

    /**
     * Set the identifier of the user associated with the token.
     *
     * @param string $identifier The identifier of the user
     */
    public function setUserIdentifier($identifier): void
    {
        $this->userIdentifier = Uuid::fromString($identifier);
    }

    /**
     * Set the client that the token was issued to.
     *
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Associate a scope with the token.
     *
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        if (! $this->scopes->contains($scope)) {
            $this->scopes->add($scope);
        }
    }
}
