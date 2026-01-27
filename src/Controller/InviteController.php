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

class InviteController extends AbstractController
{
    #[Route('/api/invitar', name: 'api_invitar', methods: ['POST'])]
    public function invitar(
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
        if (!$data || !isset($data['user_id'])) {
            return new JsonResponse(['error' => 'user_id required'], 400);
        }

        $inviter = $em->getRepository(\App\Entity\User::class)->find($payload['user_id']);
        $invitee = $em->getRepository(\App\Entity\User::class)->find($data['user_id']);
        if (!$inviter || !$invitee) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Check if already exists active private chat
        $existingChat = null;
        $inviterMembers = $em->getRepository(ChatMember::class)->findBy(['user' => $inviter, 'leftAt' => null]);
        foreach ($inviterMembers as $member) {
            $chat = $member->getChat();
            if ($chat->getType() === 'private' && $chat->isActive()) {
                $otherMembers = $em->getRepository(ChatMember::class)->findBy(['chat' => $chat]);
                foreach ($otherMembers as $om) {
                    if ($om->getUser()->getId() === $invitee->getId() && $om->getLeftAt() === null) {
                        $existingChat = $chat;
                        break 2;
                    }
                }
            }
        }

        if ($existingChat) {
            return new JsonResponse([
                'message' => 'Chat already exists',
                'chat' => [
                    'id' => $existingChat->getId(),
                    'type' => $existingChat->getType(),
                    'is_active' => $existingChat->isActive(),
                ],
            ]);
        }

        // Create new chat
        $chat = new Chat();
        $chat->setType('private');
        $chat->setIsActive(true);
        $em->persist($chat);

        // Add members
        $member1 = new ChatMember();
        $member1->setChat($chat);
        $member1->setUser($inviter);
        $em->persist($member1);

        $member2 = new ChatMember();
        $member2->setChat($chat);
        $member2->setUser($invitee);
        $em->persist($member2);

        $em->flush();

        return new JsonResponse([
            'message' => 'Chat created',
            'chat' => [
                'id' => $chat->getId(),
                'type' => $chat->getType(),
                'is_active' => $chat->isActive(),
            ],
        ]);
    }
}