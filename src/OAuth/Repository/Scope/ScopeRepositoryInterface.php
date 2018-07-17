<?php

namespace Cerberus\OAuth\Repository\Scope;

use Cerberus\OAuth\Scope;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface as OAuthScopeRepositoryInterface;

interface ScopeRepositoryInterface extends OAuthScopeRepositoryInterface
{
    public function save(Scope $scope, Scope ...$scopes): void;
    public function delete(string $identifier): void;
}
