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
}
