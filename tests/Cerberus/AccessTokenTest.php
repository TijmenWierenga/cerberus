<?php

namespace TijmenWierenga\Tests\Cerberus;

use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPStan\Testing\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\AccessToken;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class AccessTokenTest extends TestCase
{
    public function testItInstantiatesAnAccessToken()
    {
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $scopes = new ArrayCollection();
        $userId = Uuid::uuid4();
        $token = AccessToken::new($client, $scopes, $userId);

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $token);
        $this->assertEquals((string) $userId, $token->getUserIdentifier());
        $this->assertEquals($client, $token->getClient());
    }
}
