<?php

namespace Cerberus\Tests\Integration;

use Ramsey\Uuid\Uuid;
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

        $clientId = json_decode($response->getContent(), true)['data']['client']['data']['id'];

        return $clientId;
    }

    public function testClientCreateInvalidRequestMissingName()
    {
        $this->loginWithScopes('client_create');

        $this->client->request('POST', '/api/client', [
            'grant_types' => ['password', 'client_credentials'],
            'redirect_uris' => [
                'https://www.redirect.me/callback',
                'https://google.com/callback'
            ]
        ]);

        $response = $this->client->getResponse();
        $body = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('name', $body['errors']);
        $this->assertEquals("Validation failed", $body['message']);
        $this->assertContains("This field is missing.", $body['errors']['name']['errors']);
    }

    /**
     * @depends testClientCreate
     */
    public function testItFindsAClientById(string $clientId)
    {
        $this->loginWithScopes('client_read');

        $this->client->request('GET', "/api/client/{$clientId}");

        $response = $this->client->getResponse();
        $body = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('oauth-client', $body['data']['name']);
        $this->assertEquals($clientId, $body['data']['id']);
    }

    public function testItReturnsA404WhenClientIsNotFound()
    {
        $this->loginWithScopes('client_read');
        $randomUuid = Uuid::uuid4();
        $this->client->request('GET', "/api/client/{$randomUuid}");

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
