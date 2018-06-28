<?php

namespace Cerberus\Tests\Unit\OAuth\Service\User;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\Exception\IllegalScopeException;
use Cerberus\Exception\User\UsernameAlreadyTakenException;
use Cerberus\Hasher\HasherInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\Service\User\CreateUserRequest;
use Cerberus\OAuth\Service\User\UserService;
use Cerberus\OAuth\User;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class UserServiceTest extends TestCase
{
    /**
     * @var UserRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $userRepository;
    /**
     * @var ScopeRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeRepository;
    /**
     * @var HasherInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $hasher;
    /**
     * @var UserService
     */
    private $service;

    public function setUp()
    {
        $this->userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $this->scopeRepository = $this->getMockBuilder(ScopeRepositoryInterface::class)->getMock();
        $this->hasher = $this->getMockBuilder(HasherInterface::class)->getMock();
        $this->service = new UserService($this->userRepository, $this->hasher, $this->scopeRepository);
    }

    public function testItCreatesAUserWithoutScopes()
    {
        $request = new CreateUserRequest("tijmen", "a-password", []);

        $this->userRepository->expects($this->once())
            ->method("findByUsername")
            ->with("tijmen")
            ->willThrowException(EntityNotFoundException::create(User::class, "tijmen"));

        $this->hasher->expects($this->once())
            ->method("hash")
            ->with("a-password")
            ->willReturn("hashed-password");

        $this->scopeRepository->expects($this->never())
            ->method("getScopeEntityByIdentifier");

        $this->userRepository->expects($this->once())
            ->method("save")
            ->with($this->isInstanceOf(User::class));

        $result = $this->service->create($request);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals("tijmen", $result->getUsername());
        $this->assertEquals("hashed-password", $result->getPassword());
    }

    public function testItCreatesAUserWithExistingScopes()
    {
        $request = new CreateUserRequest("tijmen", "a-password", ["create_user", "update_user"]);

        $this->userRepository->expects($this->once())
            ->method("findByUsername")
            ->with("tijmen")
            ->willThrowException(EntityNotFoundException::create(User::class, "tijmen"));

        $this->hasher->expects($this->once())
            ->method("hash")
            ->with("a-password")
            ->willReturn("hashed-password");

        $this->scopeRepository->expects($this->exactly(2))
            ->method("getScopeEntityByIdentifier")
            ->withConsecutive(["create_user"], ["update_user"])
            ->willReturnOnConsecutiveCalls(new Scope("create_user"), new Scope("update_user"));

        $this->userRepository->expects($this->once())
            ->method("save")
            ->with($this->isInstanceOf(User::class));

        $result = $this->service->create($request);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals("tijmen", $result->getUsername());
        $this->assertEquals("hashed-password", $result->getPassword());
        $this->assertCount(2, $result->getScopes());
        $this->assertEquals("create_user", $result->getScopes()[0]->getIdentifier());
        $this->assertEquals("update_user", $result->getScopes()[1]->getIdentifier());
    }

    public function testItDoesNotCreateAUserWithNonExistingScopes()
    {
        $this->expectException(IllegalScopeException::class);

        $request = new CreateUserRequest("tijmen", "a-password", ["unexisting_scope"]);

        $this->userRepository->expects($this->once())
            ->method("findByUsername")
            ->with("tijmen")
            ->willThrowException(EntityNotFoundException::create(User::class, "tijmen"));

        $this->hasher->expects($this->once())
            ->method("hash")
            ->with("a-password")
            ->willReturn("hashed-password");

        $this->scopeRepository->expects($this->once())
            ->method("getScopeEntityByIdentifier")
            ->with("unexisting_scope")
            ->willReturn(false);

        $this->userRepository->expects($this->never())
            ->method("save");

        $this->service->create($request);
    }

    public function testItDoesNotCreateAUserWhenUsernameIsTaken()
    {
        $this->expectException(UsernameAlreadyTakenException::class);

        $request = new CreateUserRequest("tijmen", "a-password", []);

        $this->userRepository->expects($this->once())
            ->method("findByUsername")
            ->with("tijmen")
            ->willReturn($this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock());

        $this->service->create($request);
    }
}
