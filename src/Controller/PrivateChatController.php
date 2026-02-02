<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMember;
use App\Entity\User;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PrivateChatController extends AbstractController
{
    #[Route('/api/privado/cambiar/chat', name: 'api_private_change', methods: ['POST'])]
    public function cambiarChat(
        Request $request,
        EntityManagerInterface $em,
        JwtService $jwtService
    ): JsonResponse {
        $expected = $this->getParameter('app_api_key');
        if ($request->headers->get('X-API-KEY') !== $expected) {
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
        if (!$data || !isset($data['chat_id'])) {
            return new JsonResponse(['error' => 'chat_id required'], 400);
        }

        $user = $em->getRepository(User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $chat = $em->getRepository(Chat::class)->find($data['chat_id']);
        if (!$chat) {
            return new JsonResponse(['error' => 'Chat not found'], 404);
        }

        // Check if user is member
        $member = $em->getRepository(ChatMember::class)->findOneBy([
            'chat' => $chat,
            'user' => $user,
            'leftAt' => null
        ]);

        if (!$member) {
            return new JsonResponse(['error' => 'Not a member of this chat'], 403);
        }

        return new JsonResponse([
            'id' => $chat->getId(),
            'type' => $chat->getType(),
            'is_active' => $chat->isActive(),
        ]);
    }

    #[Route('/api/privado/salir', name: 'api_private_exit', methods: ['POST'])]
    public function salir(
        Request $request,
        EntityManagerInterface $em,
        JwtService $jwtService
    ): JsonResponse {
        $expected = $this->getParameter('app_api_key');
        if ($request->headers->get('X-API-KEY') !== $expected) {
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
        if (!$data || !isset($data['chat_id'])) {
            return new JsonResponse(['error' => 'chat_id required'], 400);
        }

        $user = $em->getRepository(User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $chat = $em->getRepository(Chat::class)->find($data['chat_id']);
        if (!$chat || $chat->getType() !== 'private') {
            return new JsonResponse(['error' => 'Chat not found or not private'], 404);
        }

        // Mark user as left
        $member = $em->getRepository(ChatMember::class)->findOneBy([
            'chat' => $chat,
            'user' => $user,
            'leftAt' => null
        ]);

        if (!$member) {
            return new JsonResponse(['error' => 'Not a member of this chat'], 403);
        }

        $member->setLeftAt(new \DateTime());
        $em->flush();

        // Check if both users have left
        $activeMembers = $em->getRepository(ChatMember::class)->findBy([
            'chat' => $chat,
            'leftAt' => null
        ]);

        if (count($activeMembers) === 0) {
            $chat->setIsActive(false);
            $em->flush();
        }

        return new JsonResponse(['message' => 'Left chat']);
    }
}
