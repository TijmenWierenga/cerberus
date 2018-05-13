<?php

namespace Cerberus\Security\Factory;

use Cerberus\Security\Listener\OAuthListener;
use Cerberus\Security\Provider\OAuthProvider;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OAuthFactory implements SecurityFactoryInterface
{

    /**
     * Configures the container services required to use the authentication listener.
     *
     * @param ContainerBuilder $container
     * @param string $id The unique id of the firewall
     * @param array $config The options array for the listener
     * @param string $userProvider The service id of the user provider
     * @param string $defaultEntryPoint
     *
     * @return array containing three values:
     *               - the provider id
     *               - the listener id
     *               - the entry point id
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.oauth.'.$id;
        $container->setDefinition($providerId, new ChildDefinition(OAuthProvider::class));

        $listenerId = 'security.authentication.listener.oauth.'.$id;
        $container->setDefinition($listenerId, new ChildDefinition(OAuthListener::class));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * Defines the position at which the provider is called.
     * Possible values: pre_auth, form, http, and remember_me.
     *
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * Defines the configuration key used to reference the provider
     * in the firewall configuration.
     *
     * @return string
     */
    public function getKey()
    {
        return 'oauth';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        // TODO: Implement addConfiguration() method.
    }
}
