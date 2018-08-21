<?php

namespace Cerberus\OAuth\Repository\Scope;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\Service\Scope\ScopeValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class MongoScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var ScopeValidator
     */
    private $scopeValidator;

    public function __construct(DocumentManager $manager, ScopeValidator $scopeValidator)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('Cerberus:Scope');
        $this->scopeValidator = $scopeValidator;
    }

    public function save(Scope $scope, Scope ...$scopes): void
    {
        array_unshift($scopes, $scope);

        foreach ($scopes as $scope) {
            $this->manager->persist($scope);
        }

        $this->manager->flush();
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        /** @var ScopeEntityInterface $scope */
        $scope = $this->repository->find($identifier);

        return $scope;
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested
     * are valid and optionally append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param Client $clientEntity
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
        return $this->scopeValidator->validateScopes($scopes, $grantType, $clientEntity, $userIdentifier);
    }

    public function delete(string $identifier): void
    {
        $scope = $this->getScopeEntityByIdentifier($identifier);

        if (! $scope) {
            throw EntityNotFoundException::create(Scope::class, $identifier);
        }

        $this->manager->remove($scope);
        $this->manager->flush();
    }
}
