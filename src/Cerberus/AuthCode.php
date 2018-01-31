<?php

namespace TijmenWierenga\Cerberus;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class AuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait, AccessTokenTrait, EntityTrait;

    /**
     * @var ClientEntityInterface
     */
    private $client;

    /**
     * @var DateTime
     */
    private $expiryDate;

    /**
     * @var Collection|ScopeEntityInterface[]
     */
    private $scopes;

    /**
     * @var string
     */
    private $userIdentifier;

    /**
     * AuthCode constructor.
     */
    public function __construct()
    {
        $this->scopes = new ArrayCollection();
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
        return $this->expiryDate;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
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
     * Set the date time when the token expires.
     *
     * @param DateTime $dateTime
     */
    public function setExpiryDateTime(DateTime $dateTime): void
    {
        $this->expiryDate = $dateTime;
    }

    /**
     * Set the identifier of the user associated with the token.
     *
     * @param string $identifier The identifier of the user
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
