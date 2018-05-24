<?php

namespace Cerberus\Tests\Integration;

use Cerberus\Security\Token\OAuthToken;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Role\Role;

abstract class BaseTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected function loginWithScopes(string ...$scopes): void
    {
        $roles = array_map(function (string $scope) {
            return new Role('ROLE_' . strtoupper($scope));
        }, $scopes);

        $token = new OAuthToken(
            $this->getMockBuilder(ServerRequestInterface::class)->getMock(),
            Uuid::uuid4()->toString(),
            Uuid::uuid4()->toString(),
            Uuid::uuid4()->toString(),
            ...$roles
        );
        $token->setAuthenticated(true);
        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $tokenStorage->setToken($token);
    }
}
