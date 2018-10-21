<?php

namespace Cerberus\OAuth\Service\User;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Exception\IllegalScopeException;
use Cerberus\Exception\User\UsernameAlreadyTakenException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\User;
use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\PropertyAccess\ObjectUpdaterInterface;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var HasherInterface
     */
    private $hasher;
    /**
     * @var ScopeRepositoryInterface
     */
    private $scopeRepository;
    /**
     * @var ObjectUpdaterInterface
     */
    private $updater;

    public function __construct(
        UserRepositoryInterface $userRepository,
        HasherInterface $hasher,
        ScopeRepositoryInterface $scopeRepository,
        ObjectUpdaterInterface $updater
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->scopeRepository = $scopeRepository;
        $this->updater = $updater;
    }

    public function create(CreateUserRequest $request): User
    {
        $username = $request->getUsername();

        try {
            $user = $this->userRepository->findByUsername($username);

            if ($user) {
                throw UsernameAlreadyTakenException::create($username);
            }
        } catch (EntityNotFoundException $e) {
            // Just continue since username is not taken yet
        }

        $password = $this->hasher->hash($request->getPassword());

        $scopes = array_map(function (string $scope) {
            $scopeObject = $this->scopeRepository->getScopeEntityByIdentifier($scope);

            if (! $scopeObject) {
                throw IllegalScopeException::create($scope);
            }

            return $scopeObject;
        }, $request->getScopes());

        $user = User::new(Uuid::uuid4(), $username, $password, $scopes);

        $this->userRepository->save($user);

        return $user;
    }

    public function findPaginated(int $page = 1, int $perPage = 10): PaginatedCollection
    {
        return $this->userRepository->findPaginated($page, $perPage);
    }

    public function find(string $id): User
    {
        return $this->userRepository->find($id);
    }

    public function update(UpdateUserRequest $request): void
    {
        $user = $this->find($request->getId());
        // @TODO: filter updateable values
        /** @var User $user */
        $user = $this->updater->update($user, $request->getValues());
        $this->userRepository->save($user);
    }
}
