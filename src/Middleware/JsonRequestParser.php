<?php

namespace Cerberus\Middleware;

use Cerberus\Exception\InvalidRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonRequestParser implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => "parseRequest"];
    }

    public function parseRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getContentType() === "json" &&
            ($request->isMethod('POST') || $request->isMethod('PUT'))
        ) {
            $result = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw InvalidRequestException::invalidJson();
            }

            $request->request->replace($result);
        }
    }
}
