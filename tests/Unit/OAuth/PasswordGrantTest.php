<?php

namespace Cerberus\Tests\Unit\OAuth;

use Cerberus\Hasher\PlainTextHasher;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\AccessToken\InMemoryAccessTokenRepository;
use Cerberus\OAuth\Repository\Client\InMemoryClientRepository;
use Cerberus\OAuth\Repository\RefreshToken\InMemoryRefreshTokenRepository;
use Cerberus\OAuth\Repository\Scope\InMemoryScopeRepository;
use Cerberus\OAuth\Repository\User\InMemoryUserRepository;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\User;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class PasswordGrantTest extends TestCase
{
    /**
     * @throws \Exception
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function testItExchangesAUsernameAndPasswordForAnAccessToken()
    {
        $client = Client::new(Uuid::uuid4(), 'test-client', '12345678', ['https://www.test.me']);
        $client->addAllowedGrantType('password');
        $scope = new Scope("test");
        $scope2 = new Scope("god");
        $user = User::new(Uuid::uuid4(), 'tijmen', 'password');
        $clientRepository = new InMemoryClientRepository(new PlainTextHasher(), new ArrayCollection([$client]));
        $scopeRepository = new InMemoryScopeRepository(new ArrayCollection([$scope, $scope2]));
        $accessTokenRepository = new InMemoryAccessTokenRepository();
        $userRepository = new InMemoryUserRepository(new ArrayCollection([$user]));
        $refreshTokenRepository = new InMemoryRefreshTokenRepository();
        $privateKey = new CryptKey(__DIR__ . '/../../private.test.key');
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            base64_encode(random_bytes(32))
        );
        $server->enableGrantType(new ClientCredentialsGrant(), new DateInterval("PT30M"));
        $server->enableGrantType(new PasswordGrant($userRepository, $refreshTokenRepository));

        $request = (new ServerRequest())
            ->withParsedBody([
                "grant_type" => "password",
                "client_id" => $client->getIdentifier(),
                "client_secret" => "12345678",
                "scope" => "test god",
                "username" => "tijmen",
                "password" => "password"
            ]);
        $response = new Response();

        $response = $server->respondToAccessTokenRequest($request, $response);
        $body = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Bearer", $body->token_type);
        $this->assertEquals(3600, $body->expires_in);
        $this->assertRegExp("/^([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_\-\+\/=]*)/", $body->access_token);
    }
}
