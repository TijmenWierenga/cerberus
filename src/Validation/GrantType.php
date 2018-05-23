<?php

namespace Cerberus\Validation;

use Symfony\Component\Validator\Constraint;

class GrantType extends Constraint
{
    public $message = "Invalid grant type";
}
