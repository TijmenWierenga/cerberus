<?php

namespace Cerberus\OAuth\Service\User;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Exception\IllegalScopeException;
use Cerberus\Exception\User\UsernameAlreadyTakenException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\User;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
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

    public function __construct(
        UserRepositoryInterface $userRepository,
        HasherInterface $hasher,
        ScopeRepositoryInterface $scopeRepository
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->scopeRepository = $scopeRepository;
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
}