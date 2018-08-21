<?php

namespace Cerberus\Exception\User;

use Cerberus\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class UsernameAlreadyTakenException extends HttpException
{
    public static function create(string $username)
    {
        return new self("Username '{$username}' is already taken");
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
