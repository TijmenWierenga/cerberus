<?php

namespace Cerberus\Tests\Unit\Security\Listener;

use Cerberus\Security\Listener\OAuthListener;
use Cerberus\Security\Token\OAuthToken;
use Cerberus\Security\Token\PreOAuthToken;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OAuthListenerTest extends TestCase
{
    /** @var GetResponseEvent|PHPUnit_Framework_MockObject_MockObject */
    private $event;
    /** @var Request */
    private $request;
    /** @var TokenStorageInterface|PHPUnit_Framework_MockObject_MockObject */
    private $tokenStorage;
    /** @var AuthenticationManagerInterface|PHPUnit_Framework_MockObject_MockObject */
    private $authenticationManager;
    /** @var OAuthListener */
    private $listener;

    public function setUp()
    {
        $this->event = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $this->request = Request::create("/api/client");
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $this->authenticationManager = $this->getMockBuilder(AuthenticationManagerInterface::class)->getMock();
        $this->listener = new OAuthListener($this->tokenStorage, $this->authenticationManager);
    }

    public function testItSuccessfullyAuthenticatesARequest()
    {
        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->tokenStorage->expects($this->exactly(2))
            ->method('setToken')
            ->withConsecutive(
                [$this->isInstanceOf(PreOAuthToken::class)],
                [$this->isInstanceOf(OAuthToken::class)] // This is the authenticated token
            );

        $this->authenticationManager->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf(PreOAuthToken::class))
            ->willReturn($this->getMockBuilder(OAuthToken::class)->disableOriginalConstructor()->getMock());

        $this->event->expects($this->never())
            ->method('setResponse');

        $this->listener->handle($this->event);
    }

    public function testItHandleFailedAuthentication()
    {
        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->tokenStorage->expects($this->exactly(2))
            ->method('setToken')
            ->withConsecutive(
                [$this->isInstanceOf(PreOAuthToken::class)],
                [null] // Token should be reset to null
            );

        $this->tokenStorage->expects($this->exactly(2))
            ->method('getToken')
            ->willReturnOnConsecutiveCalls(
                null,
                $this->getMockBuilder(PreOAuthToken::class)->disableOriginalConstructor()->getMock()
            );

        $this->authenticationManager->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf(PreOAuthToken::class))
            ->willThrowException(new AuthenticationException("Authentication failed"));

        $this->event->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(JsonResponse::class));

        $this->listener->handle($this->event);
    }
}
