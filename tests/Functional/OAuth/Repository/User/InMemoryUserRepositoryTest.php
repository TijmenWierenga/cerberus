<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\User;

use Cerberus\OAuth\Repository\User\InMemoryUserRepository;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;

class InMemoryUserRepositoryTest extends UserRepositoryTest
{
    protected function getRepository(): UserRepositoryInterface
    {
        return new InMemoryUserRepository();
    }
}
