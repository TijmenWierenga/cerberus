<?php

namespace Cerberus\Controller;

use Cerberus\OAuth\Service\User\CreateUserRequest;
use Cerberus\OAuth\Service\User\UpdateUserRequest;
use Cerberus\OAuth\Service\User\UserService;
use Cerberus\Pagination\PagerfantaPaginationAdapterFactory;
use Cerberus\Response\ResourceResponse;
use Cerberus\Transformer\UserTransformer;
use League\Fractal\Manager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends BaseController
{

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        UserService $userService,
        ValidatorInterface $validator,
        Manager $transformer,
        PagerfantaPaginationAdapterFactory $paginationAdapterFactory
    ) {
        $this->userService = $userService;

        parent::__construct($validator, $transformer, $paginationAdapterFactory);
    }

    public function create(ServerRequestInterface $request): ResourceResponse
    {
        $constraints = new Collection([
            "username" => [
                new NotBlank(),
                new Type(["type" => "alnum"])
            ],
            "password" => [
                new NotBlank(),
                new Length(["min" => 8, "max" => 64])
            ],
            "scopes" => [
                new Type(["type" => "array"])
            ]
        ]);

        $this->validate($request, $constraints);

        $request = CreateUserRequest::fromRequest($request);
        $user = $this->userService->create($request);
        $resource = $this->generateItem($user, new UserTransformer());

        return new ResourceResponse($resource, Response::HTTP_CREATED);
    }

    public function findPaginated(ServerRequestInterface $request): ResourceResponse
    {
        $collection = $this->userService->findPaginated(
            $request->getQueryParams()['page'] ?? 1,
            $request->getQueryParams()['per_page'] ?? 10
        );

        return new ResourceResponse(
            $this->generateCollection($collection, new UserTransformer(), $request),
            Response::HTTP_OK
        );
    }

    public function find(string $id): ResourceResponse
    {
        return new ResourceResponse(
            $this->generateItem($this->userService->find($id), new UserTransformer()),
            Response::HTTP_OK
        );
    }

    public function update(ServerRequestInterface $request, string $id): ResponseInterface
    {
        /** @var array $body */
        $body = $request->getParsedBody();
        // Parse body to valid request object

        $this->userService->update(new UpdateUserRequest($id, $body));

        return (new \Zend\Diactoros\Response())
            ->withStatus(Response::HTTP_NO_CONTENT)
            ->withHeader('Content-Type', 'application/json');
    }
}
