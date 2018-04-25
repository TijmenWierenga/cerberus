<?php

namespace Cerberus\Tests\Oauth\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\Oauth\Client;
use Cerberus\Oauth\Repository\Client\InMemoryClientRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryClientRepositoryTest extends TestCase
{
    public function testItReturnsAClientById()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'http://www.suchapp.com/callback');
        $repo = new InMemoryClientRepository(new ArrayCollection([$client]));

        $result = $repo->getClientEntity($client->getIdentifier(), 'client_credentials', null, false);

        $this->assertEquals($client, $result);
    }

    public function testItReturnsAClientByIdAndClientSecret()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'http://www.suchapp.com/callback');
        $repo = new InMemoryClientRepository(new ArrayCollection([$client]));

        $result = $repo->getClientEntity(
            $client->getIdentifier(),
            'client_credentials',
            $client->getClientSecret(),
            true
        );

        $this->assertEquals($client, $result);
    }

    public function testItReturnsNullIfSecretDoesNotMatch()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'http://www.suchapp.com/callback');
        $repo = new InMemoryClientRepository(new ArrayCollection([$client]));

        $result = $repo->getClientEntity(
            $client->getIdentifier(),
            'client_credentials',
            'a-wrong-secret',
            true
        );

        $this->assertNull($result);
    }
}
