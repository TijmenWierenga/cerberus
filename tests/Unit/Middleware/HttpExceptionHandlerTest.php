<?php

namespace Cerberus\Tests\Unit\Middleware;

use Cerberus\Exception\HttpException;
use Cerberus\Middleware\HttpExceptionHandler;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class HttpExceptionHandlerTest extends TestCase
{
    /**
     * @var HttpExceptionHandler
     */
    private $handler;

    public function setUp()
    {
        $this->handler = new HttpExceptionHandler();
    }

    public function testItRendersHttpResponseOnHttpException()
    {
        $exceptionClass = new class extends HttpException {
            public function getStatusCode(): int
            {
                return Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        };

        $exception = new $exceptionClass("Everything is on fire");

        /** @var KernelInterface|PHPUnit_Framework_MockObject_MockObject $kernel */
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        /** @var Request|PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $event = new GetResponseForExceptionEvent($kernel, $request, 0, $exception);

        $this->handler->handleHttpException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals("Everything is on fire", json_decode($response->getContent(), true)["message"]);
    }
}
