<?php

namespace Cerberus\Security\Listener;

use Cerberus\Security\Token\OAuthToken;
use Cerberus\Security\Token\PreOAuthToken;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class OAuthListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (! $request->headers->has('Authorization')) {
            return;
        }

        try {
            $preToken = new PreOAuthToken((new DiactorosFactory())->createRequest($request));
            $token = $this->authenticationManager->authenticate($preToken);

            $this->tokenStorage->setToken($token);
        } catch (AuthenticationException $e) {
            $this->tokenStorage->setToken(null);
        }
    }
}
