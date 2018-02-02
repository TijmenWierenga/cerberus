<?php

namespace TijmenWierenga\Cerberus\Container;

use Psr\Container\ContainerInterface;
use Slim\Container;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class SlimContainerFactory
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    public static function create(): ContainerInterface
    {
        self::$container = new Container();

        return self::$container;
    }
}
