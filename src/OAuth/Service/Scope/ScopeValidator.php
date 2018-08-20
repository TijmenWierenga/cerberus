<?php

namespace Cerberus\OAuth\Service\Scope;

use Cerberus\OAuth\Client;
use Cerberus\OAuth\Repository\User\UserRepositoryInterface;

class ScopeValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $scopes
     * @param string $grantType
     * @param Client $client
     * @param string|null $userIdentifier
     * @return array  A validated list of scopes
     */
    public function validateScopes(array $scopes, string $grantType, Client $client, ?string $userIdentifier): array
    {
        $user = null;

        if ($userIdentifier) {
            $user = $this->userRepository->find($userIdentifier);
        }

        $finalScopes = [];
        foreach ($scopes as $scope) {
            if ($client->hasScope($scope)) {
                if (! $user || $user && $user->hasScope($scope)) {
                    $finalScopes[] = $scope;
                }
            }
        }

        return $finalScopes;
    }
}
