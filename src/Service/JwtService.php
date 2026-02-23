<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\Clock\SystemClock;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JwtService
{
    private Configuration $config;

    public function __construct(ParameterBagInterface $params)
    {
        $secret = $params->get('app_secret');
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );
    }

    public function generateToken(array $payload): string
    {
        $now = new \DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user_id', $payload['user_id'])
            ->withClaim('email', $payload['email'])
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    public function validateToken(string $token): ?array
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);
            
            if (!$parsedToken instanceof Plain) {
                error_log("Token is not a Plain instance");
                return null;
            }
            
            $constraints = [
                new SignedWith($this->config->signer(), $this->config->signingKey()),
                new StrictValidAt(SystemClock::fromUTC()),
            ];

            $this->config->validator()->assert($parsedToken, ...$constraints);

            return [
                'user_id' => $parsedToken->claims()->get('user_id'),
                'email' => $parsedToken->claims()->get('email'),
            ];
        } catch (\Exception $e) {
            error_log("Token validation error: " . $e->getMessage());
            error_log("Token: " . substr($token, 0, 50) . "...");
            return null;
        }
    }
}