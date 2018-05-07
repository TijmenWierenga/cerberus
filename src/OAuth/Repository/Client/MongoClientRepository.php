<?php

namespace Cerberus\OAuth\Repository\Client;

use Cerberus\OAuth\Client;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;

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
     * MongoClientRepository constructor.
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->repository = $manager->getRepository('Cerberus:Client');
        $this->manager = $manager;
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
     * @return ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        return $this->repository->find($clientIdentifier);
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
