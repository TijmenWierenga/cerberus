<?php

namespace Cerberus\Tests\Unit\PropertyAccess;

use Cerberus\Exception\PropertyAccessException;
use Cerberus\OAuth\Client;
use Cerberus\PropertyAccess\ObjectUpdater;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectUpdaterTest extends TestCase
{
    /**
     * @var ObjectUpdater
     */
    private $updater;

    public function setUp()
    {
        $this->updater = new ObjectUpdater(PropertyAccess::createPropertyAccessor());
    }

    public function testItUpdatesPropertiesOnAnObject()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', '123456789', [
            'https://www.callback.nl'
        ], [
            'implicit'
        ]);

        $values = [
            'name' => 'paul',
            'redirect_uris' => [
                'https://www.callback.nl',
                'https://www.oauth.com'
            ],
            'allowed_grant_types' => [
                'password_grant',
                'client_credentials'
            ]
        ];

        $this->updater->update($client, $values);

        $this->assertEquals('paul', $client->getName());
        $this->assertContains('password_grant', $client->getAllowedGrantTypes());
        $this->assertContains('client_credentials', $client->getAllowedGrantTypes());
        $this->assertContains('https://www.callback.nl', $client->getRedirectUri());
        $this->assertContains('https://www.oauth.com', $client->getRedirectUri());
        $this->assertNotContains('implicit', $client->getAllowedGrantTypes());
    }

    public function testItThrowsAnExceptionWhenAPropertyDoesNotExist()
    {
        $this->expectException(PropertyAccessException::class);

        $client = Client::new(Uuid::uuid4(), 'tijmen', '123456789', []);

        $values = [
            'fake' => true
        ];

        $this->updater->update($client, $values);
    }
}
