<?php

namespace Cerberus\Tests\Unit\OAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\Oauth\Client;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class ClientTest extends TestCase
{
    public function testItInstantiatesAClientWithSingleRedirectUri()
    {
        $id = Uuid::uuid4();
        $client = Client::new($id, 'test-client', 'super-secret-code', ['http://www.redirect.me']);

        $this->assertInstanceOf(ClientEntityInterface::class, $client);
        $this->assertEquals('test-client', $client->getName());
        $this->assertEquals($id->toString(), $client->getIdentifier());
        $this->assertEquals('super-secret-code', $client->getClientSecret());
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
            'super-secret-code',
            $redirectUris
        );

        $this->assertInstanceOf(ClientEntityInterface::class, $client);
        $this->assertEquals('test-client', $client->getName());
        $this->assertEquals($id->toString(), $client->getIdentifier());
        $this->assertEquals('super-secret-code', $client->getClientSecret());
        $this->assertEquals($redirectUris, $client->getRedirectUri());
    }

    /**
     * @dataProvider requestedGrantsDataProvider
     */
    public function testItChecksForGrantTypes($expectedResult, string ...$requestedGrantTypes)
    {
        $client = Client::new(Uuid::uuid4(), 'test-client', 'secret', ['https://redirect.com']);

        $this->assertEquals($expectedResult, $client->allowsGrantType(...$requestedGrantTypes));
    }

    public function requestedGrantsDataProvider(): array
    {
        return [
            [false, 'password'],
            [true, 'auth_code'],
            [true, 'auth_code', 'implicit'],
            [false, 'auth_code', 'password'],
            [false, 'password', 'auth_code'],
            [false, 'client_credentials', 'password']
        ];
    }
}
