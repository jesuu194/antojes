<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(
        Request $request,
        EntityManagerInterface $em,
        JwtService $jwtService
    ): JsonResponse {
        // Check API Key
        $apiKey = $request->headers->get('X-API-KEY');
        $expected = $this->getParameter('app_api_key');
        if ($apiKey !== $expected) {
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

        $user = $em->getRepository(User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user->setOnline(false);
        $em->flush();

        return new JsonResponse(['message' => 'Logged out']);
    }
}