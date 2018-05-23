<?php

namespace Cerberus\Response;

use League\Fractal\Scope;

class ResourceResponse
{
    /**
     * @var Scope
     */
    private $resource;
    /**
     * @var int
     */
    private $statusCode;

    public function __construct(Scope $resource, int $statusCode)
    {
        // TODO: Assert valid status code
        $this->resource = $resource;
        $this->statusCode = $statusCode;
    }

    /**
     * @return Scope
     */
    public function getResource(): Scope
    {
        return $this->resource;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
