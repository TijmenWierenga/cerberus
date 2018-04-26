<?php

namespace Cerberus\OAuth\Repository\RefreshToken;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Cerberus\OAuth\RefreshToken;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * InMemoryRefreshTokenRepository constructor.
     * @param Collection|null $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }

    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        if ($this->collection->contains($refreshTokenEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->collection->add($refreshTokenEntity);
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId): void
    {
        $this->collection->map(function (RefreshTokenEntityInterface $refreshToken) use ($tokenId) {
            if ($refreshToken->getIdentifier() === $tokenId) {
                $this->collection->removeElement($refreshToken);
            }
        });
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        $result = $this->collection->filter(function (RefreshTokenEntityInterface $refreshToken) use ($tokenId) {
            return $refreshToken->getIdentifier() === $tokenId;
        });

        return $result->isEmpty();
    }
}
