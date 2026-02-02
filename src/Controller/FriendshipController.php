<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FriendRequest;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/api/amistad/solicitar', name: 'api_friendship_request', methods: ['POST'])]
    public function solicitar(
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

        $sender = $em->getRepository(User::class)->find($payload['user_id']);
        $receiver = $em->getRepository(User::class)->find($data['user_id']);

        if (!$sender || !$receiver) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($sender->getId() === $receiver->getId()) {
            return new JsonResponse(['error' => 'Cannot send request to yourself'], 400);
        }

        // Check if pending request already exists
        $existing = $em->getRepository(FriendRequest::class)->findOneBy([
            'senderUser' => $sender,
            'receiverUser' => $receiver,
            'status' => 'pending'
        ]);

        if ($existing) {
            return new JsonResponse(['error' => 'Request already pending'], 400);
        }

        $friendRequest = new FriendRequest();
        $friendRequest->setSenderUser($sender);
        $friendRequest->setReceiverUser($receiver);
        $friendRequest->setStatus('pending');
        $em->persist($friendRequest);
        $em->flush();

        return new JsonResponse([
            'message' => 'Friend request sent',
            'request_id' => $friendRequest->getId()
        ], 201);
    }

    #[Route('/api/amistad/aceptar', name: 'api_friendship_accept', methods: ['POST'])]
    public function aceptar(
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
        if (!$data || !isset($data['request_id'])) {
            return new JsonResponse(['error' => 'request_id required'], 400);
        }

        $receiver = $em->getRepository(User::class)->find($payload['user_id']);
        $friendRequest = $em->getRepository(FriendRequest::class)->find($data['request_id']);

        if (!$friendRequest) {
            return new JsonResponse(['error' => 'Request not found'], 404);
        }

        if ($friendRequest->getReceiverUser()->getId() !== $receiver->getId()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $friendRequest->setStatus('accepted');
        $friendRequest->setRespondedAt(new \DateTime());
        $em->flush();

        return new JsonResponse(['message' => 'Friend request accepted']);
    }

    #[Route('/api/amistad/rechazar', name: 'api_friendship_reject', methods: ['POST'])]
    public function rechazar(
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
        if (!$data || !isset($data['request_id'])) {
            return new JsonResponse(['error' => 'request_id required'], 400);
        }

        $receiver = $em->getRepository(User::class)->find($payload['user_id']);
        $friendRequest = $em->getRepository(FriendRequest::class)->find($data['request_id']);

        if (!$friendRequest) {
            return new JsonResponse(['error' => 'Request not found'], 404);
        }

        if ($friendRequest->getReceiverUser()->getId() !== $receiver->getId()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $friendRequest->setStatus('rejected');
        $friendRequest->setRespondedAt(new \DateTime());
        $em->flush();

        return new JsonResponse(['message' => 'Friend request rejected']);
    }

    #[Route('/api/amistad', name: 'api_friendship_list', methods: ['GET'])]
    public function listar(
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

        // Get accepted friendships
        $sent = $em->getRepository(FriendRequest::class)->findBy([
            'senderUser' => $user,
            'status' => 'accepted'
        ]);

        $received = $em->getRepository(FriendRequest::class)->findBy([
            'receiverUser' => $user,
            'status' => 'accepted'
        ]);

        $friends = [];
        foreach ($sent as $req) {
            $friends[] = [
                'id' => $req->getReceiverUser()->getId(),
                'name' => $req->getReceiverUser()->getName(),
                'email' => $req->getReceiverUser()->getEmail(),
            ];
        }

        foreach ($received as $req) {
            $friends[] = [
                'id' => $req->getSenderUser()->getId(),
                'name' => $req->getSenderUser()->getName(),
                'email' => $req->getSenderUser()->getEmail(),
            ];
        }

        return new JsonResponse(['friends' => $friends]);
    }

    #[Route('/api/amistad/pendientes', name: 'api_friendship_pending', methods: ['GET'])]
    public function getPending(
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

        // Get pending friendship requests sent to this user
        $pending = $em->getRepository(FriendRequest::class)->findBy([
            'receiverUser' => $user,
            'status' => 'pending'
        ]);

        $requests = [];
        foreach ($pending as $req) {
            $requests[] = [
                'id' => $req->getId(),
                'sender_id' => $req->getSenderUser()->getId(),
                'sender_name' => $req->getSenderUser()->getName(),
                'sender_email' => $req->getSenderUser()->getEmail(),
            ];
        }

        return new JsonResponse(['pending_requests' => $requests]);
    }
}
