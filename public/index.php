<?php

use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Slim\App;
use TijmenWierenga\Cerberus\Repository\AccessToken\InMemoryAccessTokenRepository;
use TijmenWierenga\Cerberus\Repository\Client\InMemoryClientRepository;
use TijmenWierenga\Cerberus\Repository\Scope\InMemoryScopeRepository;
use TijmenWierenga\Cerberus\Scope;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new \Slim\Container();

$container["clientRepository"] = function (ContainerInterface $container) {
    $clients = new ArrayCollection([
        \TijmenWierenga\Cerberus\Client::new(
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
