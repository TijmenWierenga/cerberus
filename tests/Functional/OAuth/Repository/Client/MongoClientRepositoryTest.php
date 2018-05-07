<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Client;

use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\Client\MongoClientRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MongoClientRepositoryTest extends KernelTestCase
{
    public function testItCreatesAClient()
    {
        self::bootKernel();
        /** @var MongoClientRepository $repository */
        $repository = self::$kernel->getContainer()->get('test.'.MongoClientRepository::class);
        $client = Client::new(Uuid::uuid4(), 'test-client', 'a-secret');

        $repository->save($client);

        $this->assertEquals($client, $repository->getClientEntity($client->getIdentifier(), 'password'));
    }
}
