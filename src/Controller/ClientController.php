<?php

namespace Cerberus\Controller;

use Cerberus\OAuth\Service\Client\ClientService;
use Cerberus\OAuth\Service\Client\CreateClientRequest;
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

    public function __construct(ClientService $clientService, ValidatorInterface $validator)
    {
        $this->clientService = $clientService;
        $this->validator = $validator;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $constraint = new Collection([
            'name' => new NotBlank(),
            'redirect_uris' => [
                new Type(['type' => 'array']),
                new All([new Url()])
            ]
        ]);

        $requestBody = $request->getParsedBody();

        $violations = $this->validator->validate($requestBody, $constraint);

        if ($violations->count()) {
            // TODO: Throw exception
        }

        $request = new CreateClientRequest($requestBody['name'], ...$requestBody['redirect_uris']);

        $result = $this->clientService->create($request);

        return $response;
    }
}
