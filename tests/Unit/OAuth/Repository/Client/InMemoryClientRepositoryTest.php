<?php

namespace Cerberus\Tests\Unit\OAuth\Repository;

use Cerberus\Oauth\Client;
use Cerberus\Oauth\Repository\Client\InMemoryClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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

    public function testItStoresANewClient()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'http://www.suchapp.com/callback');
        $repo = new InMemoryClientRepository(new ArrayCollection([]));

        $repo->save($client);

        $this->assertEquals($client, $repo->getClientEntity(
            $client->getIdentifier(),
            'password_grant',
            $client->getClientSecret(),
            false
        ));
    }
}
