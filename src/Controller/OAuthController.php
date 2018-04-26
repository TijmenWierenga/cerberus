<?php
namespace Cerberus\Controller;

use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

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

    public function accessTokenRequest(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();

        try {
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // TODO: Handle other failures
            throw $exception;
        }
    }
}
