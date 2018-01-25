<?php

namespace TijmenWierenga\Cerberus;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class Client implements ClientEntityInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string[]
     */
    private $redirectUri;

    /**
     * Client constructor.
     * @param UuidInterface $id
     * @param string $name
     * @param string[] $redirectUri
     */
    private function __construct(UuidInterface $id, string $name, array $redirectUri)
    {
        $this->id = $id;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
    }

    public static function new(UuidInterface $id, string $name, string ...$redirectUri): self
    {
        return new self($id, $name, $redirectUri);
    }

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string[]
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }
}
