<?php

namespace Cerberus\Tests\Unit\OAuth;

use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPStan\Testing\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\Oauth\AccessToken;
use Cerberus\Oauth\Scope;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class AccessTokenTest extends TestCase
{
    public function testItInstantiatesAnAccessToken(): AccessTokenEntityInterface
    {
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $scopes = new ArrayCollection();
        $userId = Uuid::uuid4();
        $token = AccessToken::new($client, $scopes, $userId);

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $token);
        $this->assertEquals((string) $userId, $token->getUserIdentifier());
        $this->assertEquals($client, $token->getClient());

        return $token;
    }

    /**
     * @param AccessTokenEntityInterface $token
     * @depends testItInstantiatesAnAccessToken
     */
    public function testItAddsAScopeToAnAccessToken(AccessTokenEntityInterface $token)
    {
        $scope = new Scope("my-scope");
        $token->addScope($scope);

        $this->assertContains($scope, $token->getScopes());
    }
}
