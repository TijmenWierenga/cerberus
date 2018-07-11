<?php

namespace Cerberus\Controller;

use Cerberus\OAuth\Service\User\CreateUserRequest;
use Cerberus\OAuth\Service\User\UserService;
use Cerberus\Pagination\PagerfantaPaginationAdapterFactory;
use Cerberus\Response\ResourceResponse;
use Cerberus\Transformer\UserTransformer;
use League\Fractal\Manager;
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
    )
    {
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
}
