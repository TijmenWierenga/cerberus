<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Client;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\Client\MongoClientRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MongoClientRepositoryTest extends KernelTestCase
{
    /**
     * @var MongoClientRepository
     */
    private $repository;

    public function setUp()
    {
        self::bootKernel();
        $this->repository = self::$kernel->getContainer()->get('test.'.MongoClientRepository::class);
    }

    public function testItCreatesAClient(): Client
    {
        $client = Client::new(Uuid::uuid4(), 'test-client', 'a-secret', ['https://redirect.com']);
        $client->addAllowedGrantType('password');

        $this->repository->save($client);

        $this->assertEquals($client, $this->repository->getClientEntity(
            $client->getIdentifier(),
            'password',
            null,
            false
        ));

        return $client;
    }

    /**
     * @depends testItCreatesAClient
     */
    public function testItFindsAClientAndValidatesCredentials(Client $client)
    {
        $this->assertEquals($client, $this->repository->getClientEntity(
            $client->getIdentifier(),
            'password',
            'a-secret',
            true
        ));
    }

    /**
     * @depends testItCreatesAClient
     */
    public function testItFindsAClient(Client $client)
    {
        $this->assertEquals($client, $this->repository->find($client->getIdentifier()));
    }

    public function testItFailsWhenClientCannotBeFound()
    {
        $this->expectException(EntityNotFoundException::class);

        $this->repository->find(Uuid::uuid4());
    }
}
