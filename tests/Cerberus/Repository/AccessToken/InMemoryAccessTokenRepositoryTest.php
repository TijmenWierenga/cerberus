<?php
namespace TijmenWierenga\Tests\Cerberus\Repository\AccessToken;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\Repository\AccessToken\InMemoryAccessTokenRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAccessTokenRepositoryTest extends TestCase
{
    public function testItReturnsATokenWithAUserIdentifier(): AccessTokenEntityInterface
    {
        $repository = new InMemoryAccessTokenRepository();
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $scope = $this->getMockBuilder(ScopeEntityInterface::class)->getMock();
        $scopes = [$scope];
        $userId = Uuid::uuid4();
        $token = $repository->getNewToken($client, $scopes, $userId);

        $this->assertInstanceOf(AccessTokenEntityInterface::class, $token);
        $this->assertEquals((string) $userId, $token->getUserIdentifier());
        $this->assertContains($scope, $token->getScopes());
        $this->assertEquals($client, $token->getClient());

        return $token;
    }

    /**
     * @depends testItReturnsATokenWithAUserIdentifier
     */
    public function testItPersistsAToken(AccessTokenEntityInterface $token): AccessTokenEntityInterface
    {
        $repository = new InMemoryAccessTokenRepository();
        $this->assertTrue($repository->isAccessTokenRevoked($token->getIdentifier()));

        $repository->persistNewAccessToken($token);
        $this->assertFalse($repository->isAccessTokenRevoked($token->getIdentifier()));

        return $token;
    }

    /**
     * @depends testItReturnsATokenWithAUserIdentifier
     */
    public function testItDoesNotPersistATokenTwice(AccessTokenEntityInterface $token): AccessTokenEntityInterface
    {
        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $repository = new InMemoryAccessTokenRepository();
        $repository->persistNewAccessToken($token);
        $repository->persistNewAccessToken($token);
    }

    /**
     * @depends testItReturnsATokenWithAUserIdentifier
     */
    public function testItRevokesAToken(AccessTokenEntityInterface $token)
    {
        $tokenId = $token->getIdentifier();
        $repository = new InMemoryAccessTokenRepository();
        $repository->persistNewAccessToken($token);

        $this->assertFalse($repository->isAccessTokenRevoked($tokenId));
        $repository->revokeAccessToken($tokenId);
        $this->assertTrue($repository->isAccessTokenRevoked($tokenId));
    }
}
