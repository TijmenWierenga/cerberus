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
     * @var string|null
     */
    private $userIdentifier;
    /**
     * @var Collection|ScopeEntityInterface[]
     */
    private $scopes;
    /**
     * @var string
     */
    private $identifier;

    /**
     * AccessToken constructor.
     * @param string $identifier
     * @param string|null $userIdentifier
     * @param ClientEntityInterface $client
     * @param Collection|ScopeEntityInterface[] $scopes
     * @param DateTime $expiryDateTime
     */
    private function __construct(
        string $identifier,
        ?string $userIdentifier,
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

    public static function new(ClientEntityInterface $client, Collection $scopes, ?UuidInterface $userId): self
    {
        return new self(
            (string) Uuid::uuid4(),
            (string) $userId,
            $client,
            $scopes,
            new DateTime()
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
     * @return string|int|null
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function getScopes(): array
    {
        return $this->scopes->toArray();
    }

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set the token's identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
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
     * @param string|null $identifier The identifier of the user
     */
    public function setUserIdentifier($identifier): void
    {
        $this->userIdentifier = $identifier;
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
