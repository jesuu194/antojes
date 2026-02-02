<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMember;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PrivateController extends AbstractController
{
    #[Route('/api/privado', name: 'api_privado', methods: ['GET'])]
    public function privado(
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

        $user = $em->getRepository(\App\Entity\User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $chatMembers = $em->getRepository(ChatMember::class)->findBy(['user' => $user, 'leftAt' => null]);
        $chats = [];
        foreach ($chatMembers as $member) {
            $chat = $member->getChat();
            if ($chat->getType() === 'private' && $chat->isActive()) {
                // Find the other member
                $otherMembers = $em->getRepository(ChatMember::class)->findBy(['chat' => $chat]);
                $otherUser = null;
                foreach ($otherMembers as $om) {
                    if ($om->getUser()->getId() !== $user->getId()) {
                        $otherUser = $om->getUser();
                        break;
                    }
                }
                $chats[] = [
                    'id' => $chat->getId(),
                    'type' => $chat->getType(),
                    'is_active' => $chat->isActive(),
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->getId(),
                        'name' => $otherUser->getName(),
                    ] : null,
                ];
            }
        }

        return new JsonResponse(['chats' => $chats]);
    }
}