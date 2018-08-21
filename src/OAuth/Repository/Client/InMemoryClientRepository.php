<?php
namespace Cerberus\OAuth\Repository\Client;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

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

        if (! $client->allowsGrantType($grantType)) {
            throw OAuthServerException::invalidGrant();
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
     * @param string $id
     * @return Client
     * @throws EntityNotFoundException
     */
    public function find(string $id): Client
    {
        $client = $this->getClient($id);

        if (! $client) {
            throw EntityNotFoundException::create(Client::class, $id);
        }

        return $client;
    }

    /**
     * Removes a client from the database
     *
     * @param string $id
     */
    public function delete(string $id): void
    {
        $client = $this->getClient($id);

        if (! $client) {
            throw EntityNotFoundException::create(Client::class, $id);
        }

        $this->collection->removeElement($client);
    }
}
