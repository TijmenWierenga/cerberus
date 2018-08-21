<?php

namespace Cerberus\Tests\Functional\OAuth\Repository\User;

use Cerberus\OAuth\Repository\User\MongoUserRepository;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;

class MongoUserRepositoryTest extends UserRepositoryTest
{
    protected function getRepository(): UserRepositoryInterface
    {
        return self::$kernel->getContainer()->get('test.'.MongoUserRepository::class);
    }
}
