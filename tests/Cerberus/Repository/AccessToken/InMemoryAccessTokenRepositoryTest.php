<?php
namespace TijmenWierenga\Tests\Cerberus\Repository\AccessToken;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\Repository\AccessToken\InMemoryAccessTokenRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAccessTokenRepositoryTest extends TestCase
{
    public function testItReturnsATokenWithAUserIdentifier()
    {
        $repository = new InMemoryAccessTokenRepository();
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $scopes = ["test", "another"];
        $userId = Uuid::uuid4();
        $token = $repository->getNewToken($client, $scopes, $userId);

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $token);
        $this->assertEquals((string) $userId, $token->getUserIdentifier());
        $this->assertContains("test", $token->getScopes());
        $this->assertContains("another", $token->getScopes());
        $this->assertEquals($client, $token->getClient());
    }
}
