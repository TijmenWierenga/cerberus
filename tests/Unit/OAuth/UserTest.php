<?php

namespace Cerberus\Tests\Unit\OAuth;

use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Role\Role;

class UserTest extends TestCase
{
    public function testItInstantiatesAUser()
    {
        $id = Uuid::uuid4();
        $user = User::new($id, 'tijmen', 'a-password');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('tijmen', $user->getUsername());
        $this->assertEquals('a-password', $user->getPassword());
        $this->assertEmpty($user->getScopes());
        $this->assertEquals((string) $id, $user->getIdentifier());
        $this->assertEmpty($user->getRoles());
    }

    public function testItInstantiatesAUserWithScopes()
    {
        [$first, $second] = $scopes =  [
            new Scope('client_create'),
            new Scope('client_update')
        ];
        $user = User::new(Uuid::uuid4(), 'john', 'a-password', $scopes);

        $this->assertCount(2, $user->getScopes());
        $this->assertTrue($user->hasScope($first));
        $this->assertTrue($user->hasScope($second));

        $this->assertCount(2, $user->getRoles());
        $this->assertTrue($user->hasRole(new Role("ROLE_CLIENT_CREATE")));
        $this->assertTrue($user->hasRole(new Role("ROLE_CLIENT_UPDATE")));
    }

    public function testItAddsAScopeToAUser()
    {
        $user = User::new(Uuid::uuid4(), 'john', 'a-password');

        $this->assertEmpty($user->getScopes());

        $scope = new Scope('client_delete');
        $user->addScope($scope);

        $this->assertTrue($user->hasScope($scope));
        $this->assertContains($scope, $user->getScopes());

        [$first, $second ] = $newScopes = [
            new Scope('user_create'),
            new Scope('user_remove')
        ];

        $user->addScope(...$newScopes);

        $this->assertTrue($user->hasScope($first));
        $this->assertTrue($user->hasScope($second));
        // Reassure user also has the original added scope
        $this->assertTrue($user->hasScope($scope));
    }

    public function testItRemovesAScopeFromAUser()
    {
        [$first, $second, $third] = $scopes =  [
            new Scope('client_create'),
            new Scope('client_update'),
            new Scope('user_create')
        ];
        $user = User::new(Uuid::uuid4(), 'john', 'a-password', $scopes);

        $this->assertContains($first, $user->getScopes());

        $user->removeScope($first);

        $this->assertFalse($user->hasScope($first));
        $this->assertTrue($user->hasScope($second));
        $this->assertTrue($user->hasScope($third));

        $user->removeScope($second, $third);

        $this->assertFalse($user->hasScope($second));
        $this->assertFalse($user->hasScope($third));
    }
}
