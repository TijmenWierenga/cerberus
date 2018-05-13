<?php

namespace Cerberus\Tests\Unit\Security\Provider;

use Cerberus\Security\Provider\OAuthProvider;
use Cerberus\Security\Token\PreOAuthToken;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OAuthProviderTest extends TestCase
{
    /** @var ResourceServer|PHPUnit_Framework_MockObject_MockObject */
    private $resourceServer;
    /** @var OAuthProvider */
    private $provider;

    public function setUp()
    {
        $this->resourceServer = $this->getMockBuilder(ResourceServer::class)->disableOriginalConstructor()->getMock();
        $this->provider = new OAuthProvider($this->resourceServer);
    }

    public function testItAuthenticatesAToken()
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $preToken = new PreOAuthToken($request);

        $this->resourceServer->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->with($request)
            ->willReturn($request);

        $request->expects($this->exactly(4))
            ->method('getAttribute')
            ->willReturnMap([
                ["oauth_scopes", null, ["oauth_client_create", "oauth_client_update"]],
                ["oauth_access_token_id", null, 'access_token_id'],
                ["oauth_client_id", null, "client_id"],
                ["oauth_user_id", null, "user_id"]
            ]);

        $token = $this->provider->authenticate($preToken);

        $this->assertTrue($token->isAuthenticated());
        $this->assertEquals($request, $token->getCredentials());
        $this->assertEquals("access_token_id", $token->getAccessTokenId());
        $this->assertEquals("client_id", $token->getClientId());
        $this->assertEquals("user_id", $token->getUserId());

        $expectedRoles = ["ROLE_OAUTH_CLIENT_CREATE", "ROLE_OAUTH_CLIENT_UPDATE"];
        foreach ($token->getRoles() as $role) {
            $this->assertContains($role->getRole(), $expectedRoles);
            unset($expectedRoles[$role->getRole()]);
        }
    }

    public function testItHandlesAuthenticationFailure()
    {
        $this->expectException(AuthenticationException::class);
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $preToken = new PreOAuthToken($request);

        $this->resourceServer->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->with($request)
            ->willThrowException(OAuthServerException::invalidCredentials());

        $this->provider->authenticate($preToken);
    }
}
