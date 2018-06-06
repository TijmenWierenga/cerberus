<?php

namespace Cerberus\Exception;

use Symfony\Component\HttpFoundation\Response;

class EntityNotFoundException extends HttpException
{
    public static function create(string $class, string $id): self
    {
        return new self("Entity {$class}:{$id} could not be found");
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
