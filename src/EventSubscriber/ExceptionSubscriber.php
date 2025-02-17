<?php

namespace App\EventSubscriber;

use App\Exception\CustomHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if($exception instanceof CustomHttpException) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $message = match ($exception->getStatusCode()) {
                400 => 'Bad Request',
                401, 407 => 'Unauthorized',
                403 => 'Forbidden',
                405 => 'Method Not Allowed',
                404 => 'Not Found',
                406 => 'Not Acceptable',
                408 => 'Request Timeout',
                409 => 'Conflict',
                default => 'Unexpected Error'
            };
        } else {
            $status= 500;
            $message = "An error occurred while processing your request.";
        }
        $data = [
            'status' => $status,
            'message' => $message,
        ];
        $event->setResponse(new JsonResponse($data));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
