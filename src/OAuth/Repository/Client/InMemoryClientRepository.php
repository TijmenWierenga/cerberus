<?php
namespace Cerberus\OAuth\Repository\Client;

use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Exception\UniqueEntityException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryClientRepository implements ClientRepositoryInterface
{
    /**
     * @var Collection
     */
    private $collection;
    /**
     * @var HasherInterface
     */
    private $hasher;

    /**
     * InMemoryClientRepository constructor.
     * @param HasherInterface $hasher
     * @param Collection|null $collection
     */
    public function __construct(HasherInterface $hasher, Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
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
     * @return ClientEntityInterface|null
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ?ClientEntityInterface {
        // TODO: Validate grant type
        $client = $this->getClient($clientIdentifier);

        if (! $client) {
            return null;
        }

        if ($mustValidateSecret) {
            if (! $clientSecret || ! $this->hasher->verify($client->getClientSecret(), $clientSecret)) {
                return null;
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
        $this->collection->add($client);
    }

    private function getClient(string $id): ?Client
    {
        $result = $this->collection->filter(function (ClientEntityInterface $client) use ($id) {
            return $client->getIdentifier() === $id;
        });

        if ($result->isEmpty()) {
            return null;
        }

        return $result->first();
    }
}
