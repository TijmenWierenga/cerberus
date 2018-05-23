<?php

namespace Cerberus\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GrantTypeValidator extends ConstraintValidator
{
    const GRANT_TYPES = ["password", "client_credentials", "auth_code", "implicit", "refresh_token"];

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param GrantType $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (! in_array($value, self::GRANT_TYPES)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
