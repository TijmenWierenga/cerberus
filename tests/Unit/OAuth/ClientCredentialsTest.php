<?php

namespace TijmenWierengaCerberus;

use Cerberus\Hasher\PlainTextHasher;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\ResourceServer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\AccessToken\InMemoryAccessTokenRepository;
use Cerberus\OAuth\Repository\Client\InMemoryClientRepository;
use Cerberus\OAuth\Repository\Scope\InMemoryScopeRepository;
use Cerberus\OAuth\Scope;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class ClientCredentialsTest extends TestCase
{
    /**
     * @throws \Exception
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function testItExchangesClientCredentialsForAnAccessToken()
    {
        $client = Client::new(Uuid::uuid4(), 'test-client', '12345678', ['https://www.test.me']);
        $client->addAllowedGrantType('client_credentials');
        $scope = new Scope("test");
        $scope2 = new Scope("god");
        $clientRepository = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));
        $scopeRepository = new InMemoryScopeRepository(new ArrayCollection([$scope, $scope2]));
        $accessTokenRepository = new InMemoryAccessTokenRepository();
        $privateKey = new CryptKey(__DIR__ . '/../../../keys/private.key');
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            base64_encode(random_bytes(32))
        );
        $server->enableGrantType(new ClientCredentialsGrant(), new \DateInterval("PT30M"));

        $request = (new ServerRequest())
            ->withParsedBody([
                "grant_type" => "client_credentials",
                "client_id" => $client->getIdentifier(),
                "client_secret" => "12345678",
                "scope" => "test god"
            ]);
        $response = new Response();

        $response = $server->respondToAccessTokenRequest($request, $response);
        $body = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Bearer", $body->token_type);
        $this->assertLessThanOrEqual(1800, $body->expires_in);
        $this->assertRegExp("/^([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_\-\+\/=]*)/", $body->access_token);

        $accessToken = $body->access_token;
        $resourceServer = new ResourceServer($accessTokenRepository, new CryptKey(__DIR__ . '/../../../keys/public.key'));
        $request = (new ServerRequest())
            ->withHeader('Authorization', "{$body->token_type} {$accessToken}");
        $request = $resourceServer->validateAuthenticatedRequest($request);

        $this->assertEquals($client->getIdentifier(), $request->getAttribute('oauth_client_id'));
        $this->assertContains("test", $request->getAttribute('oauth_scopes'));
        $this->assertContains("god", $request->getAttribute('oauth_scopes'));
    }
}
