<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBlock;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlockController extends AbstractController
{
    #[Route('/api/bloquear', name: 'api_block_create', methods: ['POST'])]
    public function block(
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
        if (!$data || !isset($data['user_id'])) {
            return new JsonResponse(['error' => 'user_id required'], 400);
        }

        $blocker = $em->getRepository(User::class)->find($payload['user_id']);
        $blocked = $em->getRepository(User::class)->find($data['user_id']);

        if (!$blocker || !$blocked) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($blocker->getId() === $blocked->getId()) {
            return new JsonResponse(['error' => 'Cannot block yourself'], 400);
        }

        // Check if already blocked
        $existing = $em->getRepository(UserBlock::class)->findOneBy([
            'blockerUser' => $blocker,
            'blockedUser' => $blocked
        ]);

        if ($existing) {
            return new JsonResponse(['message' => 'Already blocked']);
        }

        $block = new UserBlock();
        $block->setBlockerUser($blocker);
        $block->setBlockedUser($blocked);
        $em->persist($block);
        $em->flush();

        return new JsonResponse(['message' => 'User blocked'], 201);
    }

    #[Route('/api/bloquear/{id}', name: 'api_block_delete', methods: ['DELETE'])]
    public function unblock(
        int $id,
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

        $blocker = $em->getRepository(User::class)->find($payload['user_id']);
        $blocked = $em->getRepository(User::class)->find($id);

        if (!$blocker || !$blocked) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $block = $em->getRepository(UserBlock::class)->findOneBy([
            'blockerUser' => $blocker,
            'blockedUser' => $blocked
        ]);

        if (!$block) {
            return new JsonResponse(['error' => 'Block not found'], 404);
        }

        $em->remove($block);
        $em->flush();

        return new JsonResponse(['message' => 'User unblocked']);
    }

    #[Route('/api/bloqueados', name: 'api_blocked_list', methods: ['GET'])]
    public function getBlocked(
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

        $user = $em->getRepository(User::class)->find($payload['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $blockedUsers = $em->getRepository(UserBlock::class)->findBy([
            'blockerUser' => $user
        ]);

        $blocked = [];
        foreach ($blockedUsers as $block) {
            $blockedUser = $block->getBlockedUser();
            $blocked[] = [
                'id' => $blockedUser->getId(),
                'name' => $blockedUser->getName(),
                'email' => $blockedUser->getEmail(),
            ];
        }

        return new JsonResponse(['blocked_users' => $blocked]);
    }}