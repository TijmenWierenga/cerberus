<?php

namespace Cerberus\Tests\Unit\Middleware;

use Cerberus\Middleware\ResponseHandler;
use Cerberus\Response\ResourceResponse;
use League\Fractal\Scope;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponseHandlerTest extends TestCase
{
    public function testItSetsTheContentTypeAccordingToSupportedFormat()
    {
        /** @var GetResponseForControllerResultEvent|PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $scope = $this->getMockBuilder(Scope::class)->disableOriginalConstructor()->getMock();
        $resourceResponse = new ResourceResponse($scope, Response::HTTP_CREATED);
        $request = Request::create('/test');
        $request->headers->set('Accept', 'application/json');
        $middleware = new ResponseHandler();

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($resourceResponse);

        $scope->expects($this->once())
            ->method('toJson')
            ->willReturn($json = json_encode(['name' => 'Tijmen']));

        $event->expects($this->once())
            ->method('setResponse')
            ->with($this->equalTo(new Response($json, Response::HTTP_CREATED, [
                'Content-Type' => 'application/json'
            ])));

        $middleware->onControllerReturn($event);
    }
}
