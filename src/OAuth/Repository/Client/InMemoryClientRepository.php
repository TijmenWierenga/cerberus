<?php

namespace Cerberus\Oauth\Repository\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Cerberus\Oauth\Client;

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

        if ($mustValidateSecret && ! $client->validateSecret($clientSecret)) {
            return null;
        }

        return $client;
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
