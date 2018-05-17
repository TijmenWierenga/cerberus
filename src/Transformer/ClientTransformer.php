<?php

namespace Cerberus\Transformer;

use Cerberus\OAuth\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    public function transform(Client $client): array
    {
        return [
            "id" => $client->getIdentifier(),
            "name" => $client->getName(),
            "allowed_grant_types" => $client->getAllowedGrantTypes(),
            "redirect_uris" => $client->getRedirectUri()
        ];
    }
}
