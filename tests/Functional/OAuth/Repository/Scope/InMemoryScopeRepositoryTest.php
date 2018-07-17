<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Scope;

use Cerberus\OAuth\Repository\Scope\InMemoryScopeRepository;
use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;

class InMemoryScopeRepositoryTest extends ScopeRepositoryTest
{
    public function getRepository(): ScopeRepositoryInterface
    {
        return new InMemoryScopeRepository();
    }
}
