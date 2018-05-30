<?php

namespace Cerberus\Controller;

use Cerberus\Collection\PaginatedCollection;
use Cerberus\Exception\ValidationException;
use Cerberus\Pagination\PagerfantaPaginationAdapterFactory;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var Manager
     */
    private $transformer;
    /**
     * @var PagerfantaPaginationAdapterFactory
     */
    private $paginatorAdapterFactory;

    public function __construct(
        ValidatorInterface $validator,
        Manager $transformer,
        PagerfantaPaginationAdapterFactory $paginatorAdapterFactory
    ) {
        $this->validator = $validator;
        $this->transformer = $transformer;
        $this->paginatorAdapterFactory = $paginatorAdapterFactory;
    }

    protected function generateItem(object $object, TransformerAbstract $transformer): Scope
    {
        return $this->transformer->createData(new Item($object, $transformer));
    }

    protected function generateCollection(
        PaginatedCollection $collection,
        TransformerAbstract $transformer,
        ServerRequestInterface $request
    ): Scope {
        $scope = new ResourceCollection($collection->getItems(), $transformer);
        $scope->setPaginator($this->paginatorAdapterFactory->create($collection->getPaginator(), $request));

        return $this->transformer->createData($scope);
    }

    /**
     * @throws ValidationException
     */
    protected function validate(ServerRequestInterface $request, Collection $constraints): void
    {
        $requestBody = $request->getParsedBody();

        $violations = $this->validator->validate($requestBody, $constraints);

        if ($violations->count()) {
            throw ValidationException::fromConstraintViolationList($violations);
        }
    }
}
