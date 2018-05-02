<?php
namespace Cerberus\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuthController
{
    /**
     * @var AuthorizationServer
     */
    private $server;

    /**
     * OauthController constructor.
     * @param AuthorizationServer $server
     */
    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function accessTokenRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // TODO: Handle other failures
            throw $exception;
        }
    }
}
