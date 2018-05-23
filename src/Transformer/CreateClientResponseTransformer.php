<?php

namespace Cerberus\Transformer;

use Cerberus\OAuth\Service\Client\CreateClientResponse;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CreateClientResponseTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        "client"
    ];

    public function transform(CreateClientResponse $response): array
    {
        return [
            "client_secret" => $response->getClientSecret()
        ];
    }

    public function includeClient(CreateClientResponse $response): Item
    {
        $client = $response->getClient();

        return new Item($client, new ClientTransformer());
    }
}
