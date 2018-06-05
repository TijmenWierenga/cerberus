<?php

namespace Cerberus\OAuth\Service\Client;

use Psr\Http\Message\ServerRequestInterface;

class UpdateClientRequest
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var iterable
     */
    private $values;

    public function __construct(string $id, iterable $values)
    {
        $this->id = $id;
        $this->values = $values;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValues(): iterable
    {
        return $this->values;
    }
}
