<?php

namespace Cerberus\OAuth\Repository\Scope;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\Scope;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

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

    public function save(Scope $scope, Scope ...$scopes): void
    {
        array_unshift($scopes, $scope);

        foreach ($scopes as $scope) {
            if (! $this->collection->contains($scope)) {
                $this->collection->add($scope);
            }
        }
    }

    public function delete(string $identifier): void
    {
        $scope = $this->getScopeEntityByIdentifier($identifier);

        if (! $scope) {
            throw EntityNotFoundException::create(Scope::class, $identifier);
        }

        $this->collection->removeElement($scope);
    }
}
