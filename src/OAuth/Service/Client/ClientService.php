<?php

namespace Cerberus\OAuth\Service\Client;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\Client\ClientRepositoryInterface;
use Cerberus\PropertyAccess\ObjectUpdaterInterface;
use Ramsey\Uuid\Uuid;

class ClientService
{
    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;
    /**
     * @var HasherInterface
     */
    private $hasher;
    /**
     * @var ObjectUpdaterInterface
     */
    private $updater;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        HasherInterface $hasher,
        ObjectUpdaterInterface $updater
    ) {
        $this->clientRepository = $clientRepository;
        $this->hasher = $hasher;
        $this->updater = $updater;
    }

    public function create(CreateClientRequest $request): CreateClientResponse
    {
        $secret = base64_encode(random_bytes(32));
        $hash = $this->hasher->hash($secret);
        $client = Client::new(
            Uuid::uuid4(),
            $request->getName(),
            $hash,
            $request->getRedirectUris(),
            $request->getAllowedGrantTypes()
        );

        $this->clientRepository->save($client);

        return new CreateClientResponse($client, $secret);
    }

    public function findPaginated(int $page = 1, int $perPage = 10): PaginatedCollection
    {
        return $this->clientRepository->findPaginated($page, $perPage);
    }

    public function update(UpdateClientRequest $request): void
    {
        $client = $this->clientRepository->find($request->getId());
        /** @var Client $client */
        $client = $this->updater->update($client, $request->getValues());
        $this->clientRepository->save($client);
    }

    public function find(string $id): Client
    {
        return $this->clientRepository->find($id);
    }
}
