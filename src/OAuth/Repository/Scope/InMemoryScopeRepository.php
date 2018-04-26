<?php

namespace Cerberus\OAuth\Repository\Scope;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * InMemoryScopeRepository constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface|false
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $result = $this->collection->filter(function (ScopeEntityInterface $scope) use ($identifier) {
            return $scope->getIdentifier() === $identifier;
        });

        return $result->first();
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of
     * scopes requested are valid and optionally append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        return $scopes;
    }
}
