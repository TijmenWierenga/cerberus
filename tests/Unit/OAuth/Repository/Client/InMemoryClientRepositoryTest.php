<?php

namespace Cerberus\Tests\Unit\OAuth\Repository;

use Cerberus\Hasher\PlainTextHasher;
use Cerberus\Oauth\Client;
use Cerberus\Oauth\Repository\Client\InMemoryClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Exception\OAuthServerException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryClientRepositoryTest extends TestCase
{
    public function testItReturnsAClientById()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'a-secret', ['https://redirect.com']);
        $client->addAllowedGrantType('client_credentials');
        $repo = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));

        $result = $repo->getClientEntity($client->getIdentifier(), 'client_credentials', null, false);

        $this->assertEquals($client, $result);
    }

    public function testItDoesNotReturnIfGrantTypeIsUnsupportedForClient()
    {
        $this->expectException(OAuthServerException::class);

        // Client has no client_credentials added to allowed grant types
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'a-secret', ['https://redirect.com']);
        $repo = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));

        $repo->getClientEntity($client->getIdentifier(), 'client_credentials', null, false);
    }

    public function testItReturnsAClientByIdAndClientSecret()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'a-secret', ['https://redirect.com']);
        $client->addAllowedGrantType('client_credentials');
        $repo = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));

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
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'a-secret', ['https://redirect.com']);
        $client->addAllowedGrantType('client_credentials');
        $repo = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));

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
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'a-secret', ['https://redirect.com']);
        $client->addAllowedGrantType('password');
        $repo = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([]));

        $repo->save($client);

        $this->assertEquals($client, $repo->getClientEntity(
            $client->getIdentifier(),
            'password',
            $client->getClientSecret(),
            false
        ));
    }
}
