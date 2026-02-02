<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiKeySubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Skip API Key validation for OPTIONS requests (preflight)
        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        if (str_starts_with($path, '/api')) {
            $apiKey = $request->headers->get('X-API-KEY');
            $expected = $this->params->get('app_api_key');
            if (!$apiKey || $apiKey !== $expected) {
                $event->setResponse(new JsonResponse(['error' => 'Invalid API Key'], 401));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 100]];
    }
}
