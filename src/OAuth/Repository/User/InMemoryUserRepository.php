<?php

namespace Cerberus\Oauth\Repository\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Cerberus\Oauth\User;

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
    )
    {
        $result = $this->collection->filter(function (User $user) use ($username, $password) {
             return ($user->getUsername() === $username && $user->getPassword() === $password);
        });

        if ($result->isEmpty()) {
            return false;
        }

        return $result->first();
    }
}
