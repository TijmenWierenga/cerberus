<?php

namespace Cerberus\Security\Listener;

use Cerberus\Security\Token\OAuthToken;
use Cerberus\Security\Token\PreOAuthToken;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

        try {
            $preToken = new PreOAuthToken((new DiactorosFactory())->createRequest($request));
            $this->tokenStorage->setToken($preToken);
            $token = $this->authenticationManager->authenticate($preToken);

            $this->tokenStorage->setToken($token);
            return;
        } catch (AuthenticationException $e) {
            if ($this->tokenStorage->getToken() instanceof PreOAuthToken ||
                $this->tokenStorage->getToken() instanceof OAuthToken) {
                $this->tokenStorage->setToken(null);
            }

            $response = new JsonResponse(["message" => $e->getMessage()]);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
