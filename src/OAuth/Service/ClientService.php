<?php

namespace Cerberus\OAuth\Service;

use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Repository\Client\ClientRepositoryInterface;

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
        // Instantiate
        // Create random secret
        // Hash secret
        // Save Client
        // Return Client + secret
    }
}
