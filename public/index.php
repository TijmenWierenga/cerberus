<?php

use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Slim\App;
use TijmenWierenga\Cerberus\Client;
use TijmenWierenga\Cerberus\Repository\AccessToken\InMemoryAccessTokenRepository;
use TijmenWierenga\Cerberus\Repository\Client\InMemoryClientRepository;
use TijmenWierenga\Cerberus\Repository\RefreshToken\InMemoryRefreshTokenRepository;
use TijmenWierenga\Cerberus\Repository\Scope\InMemoryScopeRepository;
use TijmenWierenga\Cerberus\Repository\User\InMemoryUserRepository;
use TijmenWierenga\Cerberus\Scope;
use TijmenWierenga\Cerberus\User;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new \Slim\Container();

$container["clientRepository"] = function (ContainerInterface $container) {
    $clients = new ArrayCollection([
        Client::new(
            Uuid::fromString("bd523631-0b91-49e6-a099-137a647e1dee"),
            "tijmen",
            "super-secret-key",
            "http://www.testing.com"
        )
    ]);

    return new InMemoryClientRepository($clients);
};

$container["accessTokenRepository"] = function (ContainerInterface $container) {
    return new InMemoryAccessTokenRepository();
};

$container["scopeRepository"] = function (ContainerInterface $container) {
    $scopes = new ArrayCollection([new Scope("god")]);

    return new InMemoryScopeRepository($scopes);
};

$container["userRepository"] = function (ContainerInterface $container) {
    $user = User::new(Uuid::uuid4(), 'tijmen', 'password');

    return new InMemoryUserRepository(new ArrayCollection([$user]));
};

$container["refreshTokenRepository"] = function (ContainerInterface $container) {
    return new InMemoryRefreshTokenRepository();
};

$container["privateKey"] = function (ContainerInterface $container) {
    return new \League\OAuth2\Server\CryptKey(__DIR__ . "/../keys/private.key");
};

$container["encryptionKey"] = function (ContainerInterface $container) {
    return "oJtkxk1RxwFdsK/9ShMneTjQxRPsegO2Aq6eQ3NVDO0=";
};

$container["oauthServer"] = function (ContainerInterface $container) {
    $server = new AuthorizationServer(
        $container->get('clientRepository'),
        $container->get('accessTokenRepository'),
        $container->get('scopeRepository'),
        $container->get('privateKey'),
        $container->get('encryptionKey')
    );

    $server->enableGrantType(new ClientCredentialsGrant());
    $server->enableGrantType(new PasswordGrant(
        $container->get('userRepository'),
        $container->get('refreshTokenRepository')
    ));

    return $server;
};

$app = new App($container);

$app->post("/access-token", function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    /** @var AuthorizationServer $server */
    $server = $this->get("oauthServer");

    try {
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (OAuthServerException $e) {
        return $e->generateHttpResponse($response);
    }
});

$app->run();
