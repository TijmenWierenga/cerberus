<?php

namespace Cerberus\OAuth\Repository\User;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class InMemoryUserRepository implements UserRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * InMemoryUserRepository constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }


    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface|false
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $result = $this->collection->filter(function (User $user) use ($username, $password) {
             return ($user->getUsername() === $username && $user->getPassword() === $password);
        });

        if ($result->isEmpty()) {
            return false;
        }

        return $result->first();
    }

    /**
     * @param string $id
     * @return User
     * @throws EntityNotFoundException
     */
    public function find(string $id): User
    {
        $result = $this->collection->filter(function (User $user) use ($id) {
            return $user->getIdentifier() === $id;
        })->first();

        if (! $result) {
            throw EntityNotFoundException::create(User::class, $id);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param User ...$users
     */
    public function save(User $user, User ...$users): void
    {
        array_unshift($users, $user);

        foreach ($users as $user) {
            if (! $this->collection->contains($user)) {
                $this->collection->add($user);
            }
        }
    }

    /**
     * @param string $id
     * @throws EntityNotFoundException
     */
    public function delete(string $id): void
    {
        $user = $this->find($id);

        $this->collection->removeElement($user);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PaginatedCollection
     */
    public function findPaginated(int $page, int $perPage): PaginatedCollection
    {
        $paginator = new Pagerfanta(new ArrayAdapter($this->collection->toArray()));
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($page);

        return new PaginatedCollection($paginator);
    }

    /**
     * @param string $username
     * @return User
     * @throws EntityNotFoundException
     */
    public function findByUsername(string $username): User
    {
        $result = $this->collection->filter(function (User $user) use ($username) {
            return $user->getUsername() === $username;
        });

        if ($result->isEmpty()) {
            throw EntityNotFoundException::create(User::class, $username);
        }

        return $result->first();
    }
}
