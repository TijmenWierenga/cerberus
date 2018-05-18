<?php

namespace Cerberus\Middleware;

use Cerberus\Response\ResourceResponse;
use League\Fractal\Manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseHandler implements EventSubscriberInterface
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onControllerReturn'
        ];
    }

    public function onControllerReturn(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $contentType = $request->headers->has('Content-Type') ? $request->headers->get('Content-Type') : null;
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
