<?php

namespace Cerberus\Controller;

use Cerberus\Exception\ValidationException;
use League\Fractal\Manager;
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

    public function __construct(ValidatorInterface $validator, Manager $transformer)
    {
        $this->validator = $validator;
        $this->transformer = $transformer;
    }

    protected function generateItem(object $object, TransformerAbstract $transformer): Scope
    {
        return $this->transformer->createData(new Item($object, $transformer));
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
