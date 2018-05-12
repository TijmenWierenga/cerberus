<?php

namespace Cerberus\Security\Token;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;

class OAuthToken extends AbstractToken
{
    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var string
     */
    private $accessTokenId;
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string|null
     */
    private $userId;

    public function __construct(
        ServerRequestInterface $request,
        string $accessTokenId,
        string $clientId,
        ?string $userId,
        Role ...$roles)
    {
        $this->request = $request;
        $this->accessTokenId = $accessTokenId;
        $this->clientId = $clientId;
        $this->userId = $userId;

        parent::__construct($roles);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return $this->request;
    }
}
