<?php

namespace Cerberus\Tests\Integration;

use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\VarDumper\VarDumper;

class UserTest extends BaseTest
{
    public function testUserCreate()
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::$container->get('test.' . UserRepositoryInterface::class);
        /** @var ScopeRepositoryInterface $scopeRepository */
        $scopeRepository = self::$container->get('test.' . ScopeRepositoryInterface::class);
        $scopeRepository->save(new Scope('client_create'));

        $this->loginWithScopes('user_create');

        $this->client->request('POST', '/api/user', [
            'username' => 'tijmen',
            'password' => 'a-password',
            'scopes' => [
                'client_create'
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $userId = json_decode($response->getContent(), true)["data"]["id"];
        $user = $userRepository->find($userId);
        $this->assertEquals('tijmen', $user->getUsername());
        $this->assertTrue($user->hasRole(new Role("ROLE_CLIENT_CREATE")));
    }

    public function testItCannotCreateAUserWithoutTheCorrectScope()
    {
        $this->loginWithScopes('wrong_scope');

        $this->client->request('POST', '/api/user', [
            'username' => 'wrong-scope',
            'password' => 'a-password',
            'scopes' => [
                'client_create'
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testUserPaginatedResult()
    {
        $this->loginWithScopes('user_read');

        $this->client->request("GET", "/api/user?page=1&per_page=2");

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals("tijmen", $content["data"][0]["username"]);
        $this->assertEquals("client_create", $content["data"][0]["scopes"]["data"][0]["id"]);
    }

    public function testUserWasFound()
    {
        $this->loginWithScopes('user_read');

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::$container->get('test.' . UserRepositoryInterface::class);
        $user = User::new(Uuid::uuid4(), "paul", "abcdef", []);
        $userRepository->save($user);

        $this->client->request("GET", "/api/user/{$user->getIdentifier()}");

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals("paul", $content["data"]["username"]);
        $this->assertEquals($user->getIdentifier(), $content["data"]["id"]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUserWasNotFound()
    {
        $this->loginWithScopes('user_read');
        $randomId = Uuid::uuid4();
        $this->client->request("GET", "/api/user/{$randomId}");

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testItUpdatesAUser()
    {
        $this->loginWithScopes('user_read');

        /** @var ScopeRepositoryInterface $scopeRepository */
        $scopeRepository = self::$container->get('test.' . ScopeRepositoryInterface::class);
        $testScope = new Scope('user_test');
        $scopeRepository->save($testScope);
        $scopeRepository->save(new Scope('user_extra_test'));
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::$container->get('test.' . UserRepositoryInterface::class);
        $user = User::new(Uuid::uuid4(), "bart", "abcdef", [$testScope]);
        $userRepository->save($user);

        $this->client->request('PUT', "/api/user/{$user->getIdentifier()}", [
            'scopes' => [
                'user_extra_test'
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
