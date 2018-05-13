<?php

namespace Cerberus\Security\Provider;

use Cerberus\Security\Token\OAuthToken;
use Cerberus\Security\Token\PreOAuthToken;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Role\Role;

class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var ResourceServer
     */
    private $resourceServer;

    public function __construct(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return OAuthToken An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token): OAuthToken
    {
        try {
            $request = $this->resourceServer->validateAuthenticatedRequest($token->getCredentials());

            $stringScopes = $request->getAttribute('oauth_scopes');
            $roles = [];

            foreach ($stringScopes as $scope) {
                $roles[] = new Role('ROLE_' . strtoupper($scope));
            }

            $token = new OAuthToken(
                $token->getCredentials(),
                $request->getAttribute('oauth_access_token_id'),
                $request->getAttribute('oauth_client_id'),
                $request->getAttribute('oauth_user_id'),
                ...$roles
            );

            $token->setAuthenticated(true);

            return $token;
        } catch (OAuthServerException $e) {
            throw new AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof PreOAuthToken;
    }
}
