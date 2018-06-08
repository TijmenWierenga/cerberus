<?php

namespace Cerberus\Tests\Unit\OAuth\Repository\User;

use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\OAuth\Repository\User\InMemoryUserRepository;
use Cerberus\OAuth\User;

class InMemoryUserRepositoryTest extends TestCase
{
    public function testItReturnsAUserByUsernameAndPassword(): InMemoryUserRepository
    {
        $userId = Uuid::uuid4();
        $user = User::new($userId, 'tijmen', 'password');
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $repo = new InMemoryUserRepository(new ArrayCollection([$user]));
        $user = $repo->getUserEntityByUserCredentials('tijmen', 'password', 'password', $client);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getIdentifier());

        return $repo;
    }

    /**
     * @depends testItReturnsAUserByUsernameAndPassword
     * @param InMemoryUserRepository $repo
     * @return InMemoryUserRepository
     */
    public function testItReturnsFalseIfUserCannotBeFound(InMemoryUserRepository $repo): InMemoryUserRepository
    {
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
        $this->assertFalse($repo->getUserEntityByUserCredentials('henk', 'password', 'password', $client));

        return $repo;
    }

    /**
     * @depends testItReturnsFalseIfUserCannotBeFound
     * @param InMemoryUserRepository $repo
     */
    public function testItReturnsFalseIsPasswordIsWrong(InMemoryUserRepository $repo)
    {
        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();

        $this->assertFalse($repo->getUserEntityByUserCredentials('tijmen', 'false-password', 'password', $client));
    }
}
