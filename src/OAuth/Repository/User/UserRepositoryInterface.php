<?php

namespace Cerberus\OAuth\Repository\User;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\User;
use League\OAuth2\Server\Repositories\UserRepositoryInterface as OAuthUserRepositoryInterface;

interface UserRepositoryInterface extends OAuthUserRepositoryInterface
{
    /**
     * @param string $id
     * @return User
     * @throws EntityNotFoundException
     */
    public function find(string $id): User;

    /**
     * @param User $user
     * @param User ...$users
     */
    public function save(User $user, User ...$users): void; // TODO: Add a unique check

    /**
     * @param string $id
     * @throws EntityNotFoundException
     */
    public function delete(string $id): void;

    /**
     * @param int $page
     * @param int $perPage
     * @return PaginatedCollection
     */
    public function findPaginated(int $page, int $perPage): PaginatedCollection;

    /**
     * @param string $username
     * @return User
     * @throws EntityNotFoundException
     */
    public function findByUsername(string $username): User;
}
