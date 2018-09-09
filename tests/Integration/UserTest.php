<?php

namespace Cerberus\Tests\Integration;

use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
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
}
