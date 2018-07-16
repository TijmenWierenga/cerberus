<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Scope;

use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ScopeRepositoryTest extends KernelTestCase
{
    /**
     * @var ScopeRepositoryInterface
     */
    private $repository;

    abstract public function getRepository(): ScopeRepositoryInterface;

    public function setUp()
    {
        $this->repository = $this->getRepository();
    }

    public function testItAddsAScope()
    {
        $first = new Scope("client_create");
        $second = new Scope("user_create");

        $this->repository->save($first, $second);

        $this->assertEquals($first, $this->repository->getScopeEntityByIdentifier("client_create"));
        $this->assertEquals($second, $this->repository->getScopeEntityByIdentifier("user_create"));
    }

    public function testItDeletesAScope()
    {
        $scope = new Scope("token_remove");

        $this->repository->save($scope);

        $this->assertEquals($scope, $this->repository->getScopeEntityByIdentifier("token_remove"));

        $this->repository->delete($scope);

        $this->assertNull($this->repository->getScopeEntityByIdentifier("token_remove"));
    }

    /**
     * @param string $grantType
     * @param Client $client
     * @param User|null $user
     * @param array $allowedGrants
     *
     * @dataProvider finalizeScopeDataProvider
     */
    public function testItRemovesAllNonAllowedRequestedScopes(
        string $grantType,
        Client $client,
        ?User $user,
        array $allowedGrants
    ) {
        $requestedScopes = [
            new Scope("client_create"),
            new Scope ("client_remove"),
            new Scope("user_create")
        ];

        $result = $this->repository->finalizeScopes($requestedScopes, $grantType, $client, $user);

        $this->assertEquals($allowedGrants, $result);
    }

    public function finalizeScopeDataProvider(): array
    {
        return [
            [
                "client_credentials",
                Client::new(Uuid::uuid4(), "test", "test", ["https://redirect.me"]),
                null,

            ]
        ];
    }

    private function createClientWithScopes(string ...$scopes): Client
    {
        // TODO: implement
    }

    private function createUserWithScopes(string ...$scopes): User
    {
        // TODO: implement
    }
}
