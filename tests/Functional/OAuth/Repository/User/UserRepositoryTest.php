<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\User;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    abstract protected function getRepository(): UserRepositoryInterface;

    public function setUp()
    {
        self::bootKernel();
        $this->repository = $this->getRepository();
    }

    public function testItSavesAndFindsAUser()
    {
        $user = User::new(Uuid::uuid4(), 'cerberus', 'abc-not-so-safe', [
            new Scope('update_user')
        ]);

        $this->repository->save($user);

        $this->assertEquals($user, $this->repository->find($user->getIdentifier()));

        return $user;
    }

    public function testItThrowsNotFoundExceptionWhenUserCannotBeFound()
    {
        $this->expectException(EntityNotFoundException::class);

        $this->repository->find(Uuid::uuid4()); // Random UUID
    }

    /**
     * @depends testItSavesAndFindsAUser
     */
    public function testItDeletesAUser(User $user)
    {
        $this->expectException(EntityNotFoundException::class);

        $this->repository->delete($user->getIdentifier());

        $this->repository->find($user->getIdentifier());
    }

    public function testItReturnsAPaginatedCollectionOfUsers()
    {
        [$first, $second, $third] = $result = [
            User::new(Uuid::uuid4(), 'tijmen', 'a-secret'),
            User::new(Uuid::uuid4(), 'maarten', 'a-secret'),
            User::new(Uuid::uuid4(), 'paul', 'a-secret'),
        ];

        $this->repository->save(...$result);

        $result = $this->repository->findPaginated(1, 2);

        $this->assertContains($first, $result->getItems());
        $this->assertContains($second, $result->getItems());
        $this->assertNotContains($third, $result->getItems());

        $result = $this->repository->findPaginated(2, 2);

        $this->assertNotContains($first, $result->getItems());
        $this->assertNotContains($second, $result->getItems());
        $this->assertContains($third, $result->getItems());
    }
}
