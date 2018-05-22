<?php

namespace Cerberus\Tests\Unit\Middleware;

use Cerberus\Exception\InvalidRequestException;
use Cerberus\Middleware\JsonRequestParser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestParserTest extends TestCase
{
    public function testItParsesAJsonRequest()
    {
        /** @var GetResponseEvent|PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $request = Request::create('/test', 'POST', [], [], [], [], json_encode(['name' => 'Tijmen']));
        $request->headers->set('Content-Type', 'application/json');
        $middleware = new JsonRequestParser();

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $middleware->parseRequest($event);

        $this->assertEquals('Tijmen', $request->request->get('name'));
    }

    public function testItThrowsAnExceptionWhenRequestHasMalformedJson()
    {
        $this->expectException(InvalidRequestException::class);

        /** @var GetResponseEvent|PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $request = Request::create('/test', 'POST', [], [], [], [], "{some => wrong-json}");
        $request->headers->set('Content-Type', 'application/json');
        $middleware = new JsonRequestParser();

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $middleware->parseRequest($event);
    }
}
