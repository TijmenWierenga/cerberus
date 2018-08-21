<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Scope;

use Cerberus\OAuth\Repository\Scope\MongoScopeRepository;
use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;

class MongoScopeRepositoryTest extends ScopeRepositoryTest
{
    public function getRepository(): ScopeRepositoryInterface
    {
        self::bootKernel();

        return self::$container->get('test.' . MongoScopeRepository::class);
    }
}
