<?php

namespace Cerberus\Transformer;

use Cerberus\OAuth\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            "id" => $user->getIdentifier(),
            "username" => $user->getUsername(),
            "scopes" => $user->getScopes() // Fixme: Return collection of scopes with include and transformer
        ];
    }
}
