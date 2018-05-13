<?php

namespace Cerberus\OAuth\Service\Client;

use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\Client\ClientRepositoryInterface;
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

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        HasherInterface $hasher
    ) {
        $this->clientRepository = $clientRepository;
        $this->hasher = $hasher;
    }

    public function create(CreateClientRequest $request): CreateClientResponse
    {
        $secret = base64_encode(random_bytes(32));
        $hash = $this->hasher->hash($secret);
        $client = Client::new(
            Uuid::uuid4(),
            $request->getName(),
            $hash,
            ...$request->getRedirectUris()
        );

        $this->clientRepository->save($client);

        return new CreateClientResponse($client, $secret);
    }
}
