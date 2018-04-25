<?php

namespace Cerberus\Oauth;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class Scope implements ScopeEntityInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * Scope constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Get the scope's identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->id;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->id;
    }
}
