<?php

namespace Cerberus\Controller;

use Cerberus\OAuth\Service\Client\ClientService;
use Cerberus\OAuth\Service\Client\CreateClientRequest;
use Cerberus\Pagination\PagerfantaPaginationAdapterFactory;
use Cerberus\Response\ResourceResponse;
use Cerberus\Transformer\ClientTransformer;
use Cerberus\Transformer\CreateClientResponseTransformer;
use Cerberus\Validation\GrantType;
use League\Fractal\Manager;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ClientController extends BaseController
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(
        ClientService $clientService,
        ValidatorInterface $validator,
        Manager $transformer,
        PagerfantaPaginationAdapterFactory $paginationAdapterFactory
    ) {
        $this->clientService = $clientService;

        parent::__construct($validator, $transformer, $paginationAdapterFactory);
    }

    public function create(ServerRequestInterface $request): ResourceResponse
    {
        $constraints = new Collection([
            'name' => new NotBlank(),
            'redirect_uris' => [
                new Type(['type' => 'array']),
                new All([new Url()])
            ],
            'grant_types' => [
                new Type(['type' => 'array']),
                new All([new GrantType()])
            ]
        ]);

        $this->validate($request, $constraints);

        $request = CreateClientRequest::fromRequest($request);
        $content = $this->generateItem($this->clientService->create($request), new CreateClientResponseTransformer());

        return new ResourceResponse($content, Response::HTTP_CREATED);
    }

    public function find(string $id)
    {
        $content = $this->generateItem($this->clientService->find($id), new ClientTransformer());

        return new ResourceResponse($content, Response::HTTP_OK);
    }

    public function findPaginated(ServerRequestInterface $request): ResourceResponse
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $collection = $this->clientService->findPaginated($page, 5);
        $content = $this->generateCollection($collection, new ClientTransformer(), $request);

        return new ResourceResponse($content, Response::HTTP_OK);
    }
}
