<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    #[Route('/api/general', name: 'api_general', methods: ['GET'])]
    public function general(
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

        // try to fetch the general chat; some DBs may not guarantee id=1
        $chat = $em->getRepository(Chat::class)->findOneBy(['type' => 'general']);
        if (!$chat) {
            return new JsonResponse(['error' => 'General chat not found'], 404);
        }

        $messages = $em->getRepository(Message::class)->findBy(
            ['chat' => $chat],
            ['createdAt' => 'DESC'],
            50
        );

        $messageData = [];
        foreach ($messages as $message) {
            $messageData[] = [
                'id' => $message->getId(),
                'user_id' => $message->getUser()->getId(),
                'user_name' => $message->getUser()->getName(),
                'text' => $message->getText(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse([
            'chat' => [
                'id' => $chat->getId(),
                'type' => $chat->getType(),
                'is_active' => $chat->isActive(),
            ],
            'messages' => array_reverse($messageData), // oldest first
        ]);
    }
}