<?php

namespace Cerberus\Tests\Integration;

use Cerberus\OAuth\Repository\Scope\ScopeRepositoryInterface;
use Cerberus\OAuth\Scope;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseTest
{
    public function testUserCreate()
    {
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
    }
}
