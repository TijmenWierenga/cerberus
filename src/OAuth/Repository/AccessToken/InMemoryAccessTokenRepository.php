<?php
namespace Cerberus\OAuth\Repository\AccessToken;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Cerberus\OAuth\AccessToken;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var Collection
     */
    private $blacklist;

    /**
     * InMemoryAccessTokenRepository constructor.
     * @param Collection|null $collection
     */
    public function __construct(Collection $collection = null, Collection $blacklist = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
        $this->blacklist = $blacklist ?? new ArrayCollection();
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
        $this->blacklist->add($tokenId);
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
        return $this->blacklist->contains($tokenId);
    }
}
