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

        if ($user) {
            $userRepository->expects($this->once())
                ->method("find")
                ->with($user->getIdentifier())
                ->willReturn($user);
        }

        $userId = $user ? $user->getIdentifier() : null;

        $result = $validator->validateScopes($requestedScopes, $grantType, $client, $userId);

        $this->assertEquals($finalScopes, $result);
    }

    public function scopeDataProvider(): array
    {
        $userCreateScope = new Scope("user_create");
        $userUpdateScope = new Scope("user_update");
        return [
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope),
                "password",
                $this->generateUser($userCreateScope),
                [$userCreateScope]
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope),
                "password",
                $this->generateUser($userCreateScope, $userUpdateScope),
                [$userCreateScope]
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope),
                "password",
                $this->generateUser($userUpdateScope),
                []
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope, $userUpdateScope),
                "password",
                $this->generateUser($userCreateScope, $userUpdateScope),
                [$userCreateScope, $userUpdateScope]
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope, $userUpdateScope),
                "client_credentials",
                null,
                [$userCreateScope, $userUpdateScope]
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient($userCreateScope),
                "client_credentials",
                null,
                [$userCreateScope]
            ],
            [
                [$userCreateScope, $userUpdateScope],
                $this->generateClient(),
                "client_credentials",
                null,
                []
            ],
        ];
    }

    private function generateClient(Scope ...$scopes): Client
    {
        $client = Client::new(Uuid::uuid4(), "test", "test", ["https://test.com"]);

        foreach ($scopes as $scope) {
            $client->addScope($scope);
        }

        return $client;
    }

    private function generateUser(Scope ...$scopes): User
    {
        $user = User::new(Uuid::uuid4(), "test", "test");

        foreach ($scopes as $scope) {
            $user->addScope($scope);
        }

        return $user;
    }
}
