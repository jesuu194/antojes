<?php

namespace App\Controller;

use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    #[Route('/api/actualizar', name: 'api_actualizar', methods: ['POST'])]
    public function actualizar(
        Request $request,
        EntityManagerInterface $em,
        JwtService $jwtService
    ): JsonResponse {
        // Check API Key
        $apiKey = $request->headers->get('X-API-KEY');
        if ($apiKey !== 'test-api-key') {
            return new JsonResponse(['error' => 'Invalid API Key'], 401);
        }

        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token required'], 401);
        }

        $token = substr($authHeader, 7);
        $payload = $jwtService->validateToken($token);
        if (!$payload) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['lat']) || !isset($data['lng'])) {
            return new JsonResponse(['error' => 'lat and lng required'], 400);
        }

        $user = $em->getRepository(\App\Entity\User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user->setLat($data['lat']);
        $user->setLng($data['lng']);
        $em->flush();

        return new JsonResponse(['message' => 'Location updated']);
    }
}