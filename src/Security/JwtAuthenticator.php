<?php

namespace App\Security;

use App\Entity\User;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private JwtService $jwtService;
    private EntityManagerInterface $em;

    public function __construct(JwtService $jwtService, EntityManagerInterface $em)
    {
        $this->jwtService = $jwtService;
        $this->em = $em;
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api') && 
               $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('No token provided');
        }

        $token = substr($authHeader, 7);
        $payload = $this->jwtService->validateToken($token);
        
        if (!$payload) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        $userId = $payload['user_id'];
        $user = $this->em->getRepository(User::class)->find($userId);
        
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('User not found');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), fn() => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null; // Continue request
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse([
            'error' => $exception->getMessageKey()
        ], 401);
    }
}
