<?php

namespace Cerberus\Tests\Oauth\Repository\RefreshToken;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\Oauth\RefreshToken;
use Cerberus\Oauth\Repository\RefreshToken\InMemoryRefreshTokenRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryRefreshTokenRepositoryTest extends TestCase
{
    public function testItReturnsANewRefreshToken(): RefreshTokenEntityInterface
    {
        $repo = new InMemoryRefreshTokenRepository();
        $refreshToken = $repo->getNewRefreshToken();

        $this->assertInstanceOf(RefreshTokenEntityInterface::class, $refreshToken);

        return $refreshToken;
    }

    /**
     * @depends testItReturnsANewRefreshToken
     * @param RefreshTokenEntityInterface $refreshToken
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function testItPersistsAnAccessToken(RefreshTokenEntityInterface $refreshToken)
    {
        $repo = new InMemoryRefreshTokenRepository();
        $id = (string) Uuid::uuid4();
        $refreshToken->setIdentifier($id);

        $this->assertTrue($repo->isRefreshTokenRevoked($refreshToken->getIdentifier()));

        $repo->persistNewRefreshToken($refreshToken);

        $this->assertFalse($repo->isRefreshTokenRevoked($id));
    }

    public function testItCanRevokeAnAccessToken()
    {
        $repo = new InMemoryRefreshTokenRepository();
        $id = (string) Uuid::uuid4();
        $refreshToken = $repo->getNewRefreshToken();
        $refreshToken->setIdentifier($id);
        $repo->persistNewRefreshToken($refreshToken);

        $this->assertFalse($repo->isRefreshTokenRevoked($refreshToken->getIdentifier()));

        $repo->revokeRefreshToken($refreshToken->getIdentifier());

        $this->assertTrue($repo->isRefreshTokenRevoked($refreshToken->getIdentifier()));
    }
}
