<?php

namespace Cerberus\OAuth\Repository\User;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;

class MongoUserRepository implements UserRepositoryInterface
{
    /**
     * @var DocumentManager
     */
    private $manager;
    /**
     * @var HasherInterface
     */
    private $hasher;
    /**
     * @var ObjectRepository
     */
    private $repository;
    /**
     * @var Pagerfanta
     */
    private $paginator;

    public function __construct(DocumentManager $manager, HasherInterface $hasher)
    {
        $this->manager = $manager;
        $this->hasher = $hasher;
        $this->repository = $this->manager->getRepository('Cerberus:User');
        $queryBuilder = $this->manager->createQueryBuilder('Cerberus:User');
        $mongoAdapter = new DoctrineODMMongoDBAdapter($queryBuilder);
        $this->paginator = new Pagerfanta($mongoAdapter);
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
        /** @var User $user */
        $user = $this->repository->findOneBy(['username' => $username]);

        if (! $user) {
            return false;
        }

        if (! $this->hasher->verify($password, $user->getPassword())) {
            return false;
        }

        return $user;
    }

    /**
     * @param string $id
     * @return User
     * @throws EntityNotFoundException
     */
    public function find(string $id): User
    {
        /** @var User $user */
        $user = $this->repository->find($id);

        if (! $user) {
            throw EntityNotFoundException::create(User::class, $id);
        }

        return $user;
    }

    /**
     * @param User $user
     */
    public function save(User $user, User ...$users): void
    {
        array_unshift($users, $user);

        foreach ($users as $user) {
            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    /**
     * @param string $id
     * @throws EntityNotFoundException
     */
    public function delete(string $id): void
    {
        $user = $this->find($id);

        $this->manager->remove($user);
        $this->manager->flush();
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PaginatedCollection
     */
    public function findPaginated(int $page, int $perPage): PaginatedCollection
    {
        $this->paginator->setMaxPerPage($perPage);
        $this->paginator->setCurrentPage($page);

        return new PaginatedCollection($this->paginator);
    }

    /**
     * @param string $username
     * @return User
     * @throws EntityNotFoundException
     */
    public function findByUsername(string $username): User
    {
        /** @var User|null $user */
        $user = $this->repository->findOneBy(['username' => $username]);

        if (! $user) {
            throw EntityNotFoundException::create(User::class, $username);
        }

        return $user;
    }
}
