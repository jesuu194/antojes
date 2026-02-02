<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CorsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', -9999],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Always respond to OPTIONS with 200 OK and CORS headers
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-KEY, Authorization');
            $response->headers->set('Access-Control-Max-Age', '3600');
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        
        // If it's an OPTIONS request, respond with CORS headers
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-KEY, Authorization');
            $response->headers->set('Access-Control-Max-Age', '3600');
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-KEY, Authorization');
    }
}

