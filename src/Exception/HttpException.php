<?php

namespace Cerberus\Exception;

use RuntimeException;

abstract class HttpException extends RuntimeException
{
    abstract public function getStatusCode(): int;
}
