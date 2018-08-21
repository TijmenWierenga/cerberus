<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\Scope;

use Cerberus\OAuth\Repository\Scope\InMemoryScopeRepository;
use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Service\Scope\ScopeValidator;

class InMemoryScopeRepositoryTest extends ScopeRepositoryTest
{
    public function getRepository(): ScopeRepositoryInterface
    {
        $scopeValidator = new ScopeValidator($this->getMockBuilder(UserRepositoryInterface::class)->getMock());
        return new InMemoryScopeRepository($scopeValidator);
    }
}
