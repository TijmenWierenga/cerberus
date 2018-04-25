<?php

namespace TijmenWierenga\Tests\Cerberus\Repository\AuthCode;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\Repository\AuthCode\InMemoryAuthCodeRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryAuthCodeRepositoryTest extends TestCase
{
    public function testItReturnsANewAuthCodeEntity(): AuthCodeEntityInterface
    {
        $repository = new InMemoryAuthCodeRepository();
        $authCode = $repository->getNewAuthCode();
        $authCode->setIdentifier(Uuid::uuid4());

        $this->assertInstanceOf(AuthCodeEntityInterface::class, $authCode);

        return $authCode;
    }

    /**
     * @depends testItReturnsANewAuthCodeEntity
     * @param AuthCodeEntityInterface $authCode
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    public function testItStoresAndRevokesAnAuthCode(AuthCodeEntityInterface $authCode)
    {
        $repository = new InMemoryAuthCodeRepository();
        $this->assertTrue($repository->isAuthCodeRevoked($authCode->getIdentifier()));

        $repository->persistNewAuthCode($authCode);
        $this->assertFalse($repository->isAuthCodeRevoked($authCode->getIdentifier()));

        $repository->revokeAuthCode($authCode->getIdentifier());
        $this->assertTrue($repository->isAuthCodeRevoked($authCode->getIdentifier()));
    }
}
