<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\User;

use Cerberus\Exception\EntityNotFoundException;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
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

    /**
     * Should be the first test since it depends on the order in which the users appear
     */
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

    public function testItSavesAndFindsAUser()
    {
        $user = User::new(Uuid::uuid4(), 'cerberus', 'abc-not-so-safe');

        $this->repository->save($user);

        $this->assertEquals($user, $result = $this->repository->find($user->getIdentifier()));

        return $user;
    }

    public function testItThrowsNotFoundExceptionWhenUserCannotBeFound()
    {
        $this->expectException(EntityNotFoundException::class);

        $this->repository->find(Uuid::uuid4()); // Random UUID
    }

    public function testItDeletesAUser()
    {
        $this->expectException(EntityNotFoundException::class);

        $user = User::new(Uuid::uuid4(), "test-user", "test-password");
        $this->repository->save($user);

        try {
            $this->repository->delete($user->getIdentifier());
        } catch (EntityNotFoundException $e) {
            $this->fail("User did not exist on removal");
        }

        $this->repository->find($user->getIdentifier());
    }

    public function testItFindsAUserBasedOnUsernameAndPassword()
    {
        $user = User::new(Uuid::uuid4(), 'oauth', 'abc-not-so-safe');
        $this->repository->save($user);

        $client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();

        $result = $this->repository->getUserEntityByUserCredentials(
            $user->getUsername(),
            $user->getPassword(),
            'password',
            $client
        );

        $this->assertEquals($user, $result);
    }

    public function testItFindsAUserByUsername()
    {
        $user = User::new(Uuid::uuid4(), 'john', 'abc-not-so-safe');
        $this->repository->save($user);

        $result = $this->repository->findByUsername('john');

        $this->assertEquals($user, $result);
    }

    public function testItFailsWhenUsernameDoesNotExistYet()
    {
        $this->expectException(EntityNotFoundException::class);

        $this->repository->findByUsername('some-really-random-unexisting-username');
    }
}
