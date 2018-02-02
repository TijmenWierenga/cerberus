<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$container = \TijmenWierenga\Cerberus\Container\SlimContainerFactory::create();
$app = new \Slim\App($container);

$app->post('/access-token', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $response;
});

$app->run();
