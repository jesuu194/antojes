<?php

namespace App\Controller;

use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    #[Route('/api/debug/token', name: 'api_debug_token', methods: ['POST'])]
    public function debugToken(Request $request, JwtService $jwtService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['token'])) {
            return new JsonResponse(['error' => 'No token provided'], 400);
        }
        
        $token = $data['token'];
        $result = $jwtService->validateToken($token);
        
        return new JsonResponse([
            'token_received' => substr($token, 0, 50) . '...',
            'validation_result' => $result,
            'is_valid' => $result !== null
        ]);
    }
}
