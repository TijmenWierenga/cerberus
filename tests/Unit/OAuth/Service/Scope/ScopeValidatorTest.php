<?php

namespace Cerberus\Tests\Unit\OAuth\Service\Scope;

use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;
use Cerberus\OAuth\Scope;
use Cerberus\OAuth\Service\Scope\ScopeValidator;
use Cerberus\OAuth\User;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Ramsey\Uuid\Uuid;

class ScopeValidatorTest extends TestCase
{
    private $scopes = [];

    /**
     * @dataProvider scopeDataProvider
     */
    public function testItValidatesScopes(
        array $requestedScopes,
        Client $client,
        string $grantType,
        ?User $user,
        array $finalScopes
    ) {
        /** @var UserRepositoryInterface|PHPUnit_Framework_MockObject_MockObject $userRepository */
        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $validator = new ScopeValidator($userRepository);

        $userRepository->expects($this->once())
            ->method("find")
            ->with($user->getIdentifier())
            ->willReturn($user);

        $result = $validator->validateScopes($requestedScopes, $grantType, $client, $user->getIdentifier());

        $this->assertEquals($finalScopes, $result);
    }

    public function scopeDataProvider(): array
    {
        return [
            [
                $this->generateScopes("user_create", "user_update"),
                $this->generateClient("user_create"),
                "password",
                $this->generateUser("user_create"),
                [new Scope("user_create")]
            ]
        ];
    }

    private function generateScopes(string ...$scopes): array
    {
        foreach ($scopes as $scope) {
            $this->scopes[] = new Scope($scope);
        }

        return $this->scopes;
    }

    private function generateClient(string ...$scopes): Client
    {
        $client = Client::new(Uuid::uuid4(), "test", "test", ["https://test.com"]);

        $scopes = array_filter($this->scopes, function (Scope $scope) use ($scopes) {
            return in_array($scope->getIdentifier(), $scopes);
        });

        foreach ($scopes as $scope) {
            $client->addScope($scope);
        }

        return $client;
    }

    private function generateUser(string ...$scopes): User
    {
        $user = User::new(Uuid::uuid4(), "test", "test");

        $scopes = array_filter($this->scopes, function (Scope $scope) use ($scopes) {
            return in_array($scope->getIdentifier(), $scopes);
        });

        foreach ($scopes as $scope) {
            $user->addScope($scope);
        }

        return $user;
    }
}
