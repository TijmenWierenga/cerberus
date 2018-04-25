<?php
namespace Cerberus\Oauth\Repository\AccessToken;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Cerberus\Oauth\AccessToken;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAccessTokenRepository implements AccessTokenRepositoryInterface
{
    private $collection;

    /**
     * InMemoryAccessTokenRepository constructor.
     * @param Collection|null $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }

    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null // @TODO: Handle null
    ): AccessTokenEntityInterface {
        return AccessToken::new($clientEntity, new ArrayCollection($scopes), $userIdentifier);
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        if ($this->collection->contains($accessTokenEntity)) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->collection->add($accessTokenEntity);
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId): void
    {
        $result = $this->collection->filter(function (AccessTokenEntityInterface $token) use ($tokenId) {
            return $token->getIdentifier() === $tokenId;
        });

        if (! $result->isEmpty()) {
            $this->collection->removeElement($result->first());
        }
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $result = $this->collection->filter(function (AccessTokenEntityInterface $token) use ($tokenId) {
            return $token->getIdentifier() === $tokenId;
        });

        if (! $result->isEmpty()) {
            return false;
        }

        return true;
    }
}
