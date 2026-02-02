<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    #[Route('/api/config', name: 'api_config', methods: ['GET'])]
    public function config(Request $request): JsonResponse
    {
        // ApiKey validation is handled by ApiKeySubscriber on kernel request
        // Read from environment variables (try multiple sources) to avoid missing parameter issues
        $gmap = $_ENV['APP_GOOGLE_MAPS_KEY'] ?? $_SERVER['APP_GOOGLE_MAPS_KEY'] ?? getenv('APP_GOOGLE_MAPS_KEY');

        return new JsonResponse([
            'gmap_api_key' => $gmap === false ? null : $gmap,
        ]);
    }
}
