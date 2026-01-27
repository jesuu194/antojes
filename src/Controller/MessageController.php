<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMember;
use App\Entity\Message;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/api/mensaje', name: 'api_mensaje', methods: ['GET', 'POST'])]
    public function mensaje(
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

        $user = $em->getRepository(\App\Entity\User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $chatId = $request->query->get('chat_id');
        if (!$chatId) {
            return new JsonResponse(['error' => 'chat_id required'], 400);
        }

        $chat = $em->getRepository(Chat::class)->find($chatId);
        if (!$chat) {
            return new JsonResponse(['error' => 'Chat not found'], 404);
        }

        // Check if user is member
        $member = $em->getRepository(ChatMember::class)->findOneBy(['chat' => $chat, 'user' => $user, 'leftAt' => null]);
        if (!$member && $chat->getId() !== 1) {
            return new JsonResponse(['error' => 'Not a member of this chat'], 403);
        }

        if ($request->isMethod('POST')) {
            // Send message
            $data = json_decode($request->getContent(), true);
            if (!$data || !isset($data['text'])) {
                return new JsonResponse(['error' => 'text required'], 400);
            }

            // Check if user is member (skip for general chat)
            $member = $em->getRepository(ChatMember::class)->findOneBy(['chat' => $chat, 'user' => $user, 'leftAt' => null]);
            if (!$member && $chat->getId() !== 1) {
                return new JsonResponse(['error' => 'Not a member of this chat'], 403);
            }

            $message = new Message();
            $message->setChat($chat);
            $message->setUser($user);
            $message->setText($data['text']);
            $em->persist($message);
            $em->flush();

            return new JsonResponse([
                'message' => 'Message sent',
                'id' => $message->getId(),
            ]);
        } else {
            // Get messages
            // Check if user is member (skip for general chat)
            $member = $em->getRepository(ChatMember::class)->findOneBy(['chat' => $chat, 'user' => $user, 'leftAt' => null]);
            if (!$member && $chat->getId() !== 1) {
                return new JsonResponse(['error' => 'Not a member of this chat'], 403);
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

            return new JsonResponse(['messages' => array_reverse($messageData)]);
        }
    }
}