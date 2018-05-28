<?php

namespace Cerberus\Tests\Integration;

use Symfony\Component\HttpFoundation\Response;

class ClientTest extends BaseTest
{
    public function testClientCreate()
    {
        $this->loginWithScopes('client_create');

        $this->client->request('POST', '/api/client', [
            'name' => 'oauth-client',
            'grant_types' => ['password', 'client_credentials'],
            'redirect_uris' => [
                'https://www.redirect.me/callback',
                'https://google.com/callback'
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        // TODO: Add assertions to check if Client is in database
    }
}
