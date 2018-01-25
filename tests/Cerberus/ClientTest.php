<?php

namespace TijmenWierenga\Tests\Cerberus;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\Client;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class ClientTest extends TestCase
{
    public function testItInstantiatesAClientWithSingleRedirectUri()
    {
        $id = Uuid::uuid4();
        $client = Client::new($id, 'test-client', 'http://www.redirect.me');

        $this->assertInstanceOf(ClientEntityInterface::class, $client);
        $this->assertEquals('test-client', $client->getName());
        $this->assertEquals($id->toString(), $client->getIdentifier());
        $this->assertContains('http://www.redirect.me', $client->getRedirectUri());
    }

    /**
     * @test
     */
    public function testItInstantiatesAClientWithMultipleRedirectUris()
    {
        $redirectUris = [
            'http://www.redirect.me',
            'http://www.redirect-again.com',
            'http://some-more-redirect.nl/callback'
        ];
        $id = Uuid::uuid4();
        $client = Client::new(
            $id,
            'test-client',
            ...$redirectUris
        );

        $this->assertInstanceOf(ClientEntityInterface::class, $client);
        $this->assertEquals('test-client', $client->getName());
        $this->assertEquals($id->toString(), $client->getIdentifier());
        $this->assertEquals($redirectUris, $client->getRedirectUri());
    }
}
