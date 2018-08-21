<?php

namespace Cerberus\Transformer;

use Cerberus\OAuth\User;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ["scopes"];

    public function transform(User $user): array
    {
        return [
            "id" => $user->getIdentifier(),
            "username" => $user->getUsername(),
        ];
    }

    public function includeScopes(User $user): Collection
    {
        return new Collection($user->getScopes(), new ScopeTransformer());
    }
}
