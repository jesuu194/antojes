<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/api/home', name: 'api_home', methods: ['GET'])]
    public function home(
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

        $currentUser = $em->getRepository(User::class)->find($payload['user_id']);
        if (!$currentUser) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Get users within 5km
        $users = $em->getRepository(User::class)->findAll();
        $nearbyUsers = [];
        foreach ($users as $user) {
            if ($user->getId() === $currentUser->getId()) continue;
            if ($user->getLat() && $user->getLng() && $currentUser->getLat() && $currentUser->getLng()) {
                $distance = $this->haversineDistance(
                    $currentUser->getLat(), $currentUser->getLng(),
                    $user->getLat(), $user->getLng()
                );
                if ($distance <= 5) {
                    $nearbyUsers[] = [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'distance' => round($distance, 2),
                    ];
                }
            }
        }

        return new JsonResponse([
            'user' => [
                'id' => $currentUser->getId(),
                'name' => $currentUser->getName(),
                'email' => $currentUser->getEmail(),
                'lat' => $currentUser->getLat(),
                'lng' => $currentUser->getLng(),
                'online' => $currentUser->isOnline(),
            ],
            'nearby_users' => $nearbyUsers,
        ]);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}