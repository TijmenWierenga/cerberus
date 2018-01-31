<?php

namespace TijmenWierenga\Cerberus\Repository\AuthCode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use TijmenWierenga\Cerberus\AuthCode;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * InMemoryAuthCodeRepository constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }

    /**
     * Creates a new AuthCode
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCode();
    }

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        if ($this->collection->contains($authCodeEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->collection->add($authCodeEntity);
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId): void
    {
        $result = $this->collection->filter(function (AuthCodeEntityInterface $authCode) use ($codeId) {
            return $authCode->getIdentifier() === $codeId;
        });

        if (! $result->isEmpty()) {
            $this->collection->removeElement($result->first());
        }
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        $result = $this->collection->filter(function (AuthCodeEntityInterface $authCode) use ($codeId) {
            return $authCode->getIdentifier() === $codeId;
        });

        return $result->isEmpty();
    }
}
