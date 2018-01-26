<?php

namespace TijmenWierenga\Cerberus\Repository\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

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
     * InMemoryClientRepository constructor.
     * @param Collection|null $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
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
    public function getClientEntity(
        $clientIdentifier,
        $grantType,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ClientEntityInterface {
        return $this->getClient($clientIdentifier);
    }

    private function getClient(string $id): ClientEntityInterface
    {
        $client = $this->collection->filter(function (ClientEntityInterface $client) use ($id) {
            return $client->getIdentifier() === $id;
        })->first(); // TODO: Check for failure. Id might not exist.

        return $client;
    }
}
