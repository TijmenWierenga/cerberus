<?php

namespace Cerberus\Controller;

use Cerberus\OAuth\Service\Client\ClientService;
use Cerberus\OAuth\Service\Client\CreateClientRequest;
use Cerberus\Transformer\CreateClientResponseTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController
{
    /**
     * @var ClientService
     */
    private $clientService;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var Manager
     */
    private $transformer;

    public function __construct(ClientService $clientService, ValidatorInterface $validator, Manager $transformer)
    {
        $this->clientService = $clientService;
        $this->validator = $validator;
        $this->transformer = $transformer;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $constraint = new Collection([
            'name' => new NotBlank(),
            'redirect_uris' => [
                new Type(['type' => 'array']),
                new All([new Url()])
            ],
            'grant_types' => [
                new Type(['type' => 'array']),
                // TODO: Only allow existing grant types
                new All([new Type(['type' => 'string'])])
            ]
        ]);

        $requestBody = $request->getParsedBody();

        $violations = $this->validator->validate($requestBody, $constraint);

        if ($violations->count()) {
            // TODO: Throw exception
        }

        $request = new CreateClientRequest($requestBody['name'], $requestBody['redirect_uris'], $requestBody['grant_types']);
        $resource = new Item($this->clientService->create($request), new CreateClientResponseTransformer());
        $content = $this->transformer->createData($resource);

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($content->toJson());

        return $response;
    }
}
