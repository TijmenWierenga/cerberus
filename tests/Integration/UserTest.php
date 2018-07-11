<?php

namespace Cerberus\Tests\Integration;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

class UserTest extends BaseTest
{
    public function testUserCreate()
    {
        $this->loginWithScopes('user_create');

        $this->client->request('POST', '/api/user', [
            'username' => 'tijmen',
            'password' => 'a-password',
            'scopes' => [
                'client_create'
            ]
        ]);

        $response = $this->client->getResponse();
        VarDumper::dump($response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
}
