<?php

namespace Cerberus\OAuth\Repository\Client;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Exception\OAuthServerException;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pagerfanta\Pagerfanta;

class MongoClientRepository implements ClientRepositoryInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;
    /**
     * @var DocumentManager
     */
    private $manager;
    /**
     * @var HasherInterface
     */
    private $hasher;
    /**
     * @var Pagerfanta
     */
    private $paginator;

    /**
     * MongoClientRepository constructor.
     * @param DocumentManager $manager
     * @param HasherInterface $hasher
     */
    public function __construct(DocumentManager $manager, HasherInterface $hasher)
    {
        $this->repository = $manager->getRepository('Cerberus:Client');
        $this->manager = $manager;
        $this->hasher = $hasher;
        $queryBuilder = $this->manager->createQueryBuilder('Cerberus:Client');
        $mongoAdapter = new DoctrineODMMongoDBAdapter($queryBuilder);
        $this->paginator = new Pagerfanta($mongoAdapter);
    }

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param string $grantType The grant type used
     * @param null|string $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return Client|null
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ?Client {
        /** @var Client|null $client */
        $client = $this->repository->find($clientIdentifier);

        if (! $client) {
            return null;
        }

        if (! $client->allowsGrantType($grantType)) {
            throw OAuthServerException::invalidGrant();
        }

        if ($mustValidateSecret) {
            if (! $clientSecret) {
                throw OAuthServerException::invalidCredentials();
            }

            if (! $this->hasher->verify($clientSecret, $client->getClientSecret())) {
                throw OAuthServerException::invalidCredentials();
            }
        }

        return $client;
    }

    /**
     * Saves a new Client to the database
     *
     * @param Client $client
     */
    public function save(Client $client): void
    {
        $this->manager->persist($client);
        $this->manager->flush();
    }

    public function findPaginated(int $page, int $perPage): PaginatedCollection
    {
        $this->paginator->setMaxPerPage($perPage);
        $this->paginator->setCurrentPage($page);
        $items = $this->paginator->getCurrentPageResults();

        return new PaginatedCollection($items, $this->paginator);
    }

    /**
     * @param string $id
     * @return Client
     * @throws EntityNotFoundException
     */
    public function find(string $id): Client
    {
        /** @var Client $client */
        $client = $this->repository->find($id);

        if (! $client) {
            throw EntityNotFoundException::create(Client::class, $id);
        }

        return $client;
    }

    /**
     * Removes a client from the database
     *
     * @param string $id
     * @throws EntityNotFoundException
     */
    public function delete(string $id): void
    {
        $client = $this->find($id);

        $this->manager->remove($client);
        $this->manager->flush($client);
    }
}
