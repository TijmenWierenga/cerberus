<?php

namespace Cerberus\Exception;

use Symfony\Component\HttpFoundation\Response;

class IllegalScopeException extends HttpException
{
    public static function create(string $scope)
    {
        return new self("The scope '{$scope}' does not exist");
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
