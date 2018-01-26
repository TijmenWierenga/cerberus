<?php

namespace TijmenWierenga\Cerberus;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class RefreshToken implements RefreshTokenEntityInterface
{
    use RefreshTokenTrait, EntityTrait;
}
