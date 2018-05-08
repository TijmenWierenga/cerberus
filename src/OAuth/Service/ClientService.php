<?php

namespace Cerberus\OAuth\Service;

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

    public function __construct(ClientRepositoryInterface $clientRepository, HasherInterface $hasher)
    {
        $this->clientRepository = $clientRepository;
        $this->hasher = $hasher;
    }

    public function create($request)
    {
        // TODO: Create from request

        $secret = base64_encode(random_bytes(32));
        $hash = $this->hasher->hash($secret);
        $client = Client::new(
            Uuid::uuid4(),
            'testing-client',
            $hash
        );

        $this->clientRepository->save($client);

        // TODO: Return Client + secret
    }
}
