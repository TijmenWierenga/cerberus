<?php

namespace Cerberus\Middleware;

use Cerberus\Response\ResourceResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onControllerReturn'
        ];
    }

    public function onControllerReturn(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $contentType = $request->headers->has('Accept') ? $request->headers->get('Accept') : null;
        $result = $event->getControllerResult();

        if ($result instanceof ResourceResponse) {
            switch ($contentType) {
                default:
                    $content = $result->getResource()->toJson();
                    $event->setResponse(new Response($content, $result->getStatusCode(), [
                        'Content-Type' => 'application/json'
                    ]));
            }
        }
    }
}
