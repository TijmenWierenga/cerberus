<?php

namespace Cerberus\OAuth\Repository\Client;

use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Exception\OAuthServerException;

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
     * MongoClientRepository constructor.
     * @param DocumentManager $manager
     * @param HasherInterface $hasher
     */
    public function __construct(DocumentManager $manager, HasherInterface $hasher)
    {
        $this->repository = $manager->getRepository('Cerberus:Client');
        $this->manager = $manager;
        $this->hasher = $hasher;
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
    ): ?Client
    {
        /** @var Client|null $client */
        $client = $this->repository->find($clientIdentifier);

        if (! $client) {
            return null;
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
}
