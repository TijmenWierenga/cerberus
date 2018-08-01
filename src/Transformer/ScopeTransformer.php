<?php

namespace Cerberus\Transformer;

use Cerberus\OAuth\Scope;
use League\Fractal\TransformerAbstract;

class ScopeTransformer extends TransformerAbstract
{
    public function transform(Scope $scope)
    {
        return [
            "id" => $scope->getIdentifier()
        ];
    }
}
