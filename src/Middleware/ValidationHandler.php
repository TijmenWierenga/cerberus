<?php
namespace Cerberus\Middleware;

use Cerberus\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationHandler implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'handleException'
        ];
    }

    public function handleException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof ValidationException) {
            $errors = [];
            /** @var ConstraintViolationInterface $violation */
            foreach ($exception->getViolationList() as $violation) {
                $result = $this->propertyAccessor->getValue($errors, $violation->getPropertyPath());
                $result["errors"][] = $violation->getMessage();
                $result["value"] = $violation->getInvalidValue();

                $this->propertyAccessor->setValue($errors, $violation->getPropertyPath(), $result);
            }

            $response = new JsonResponse([
                "message" => $exception->getMessage(),
                "errors" => $errors
            ], Response::HTTP_BAD_REQUEST);

            $event->stopPropagation();
            $event->setResponse($response);
        }
    }
}
