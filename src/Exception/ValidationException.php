<?php

namespace Cerberus\Exception;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends RuntimeException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violationList;

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct("Validation failed");
        $this->violationList = $violationList;
    }

    public static function fromConstraintViolationList(ConstraintViolationListInterface $violationList): self
    {
        return new self($violationList);
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
