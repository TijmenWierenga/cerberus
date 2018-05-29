<?php
namespace Cerberus\OAuth\Repository\Client;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\OAuth\Client;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface as OAuthClientRepositoryInterface;

interface ClientRepositoryInterface extends OAuthClientRepositoryInterface
{
    /**
     * Saves a new Client to the database
     *
     * @param Client $client
     */
    public function save(Client $client): void;

    /**
     * Returns a paginated list of clients
     *
     * @param int $page
     * @param int $perPage
     * @return PaginatedCollection
     */
    public function findPaginated(int $page, int $perPage): PaginatedCollection;
}
