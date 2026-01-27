<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
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
            $constraints = [
                new SignedWith($this->config->signer(), $this->config->signingKey()),
                new ValidAt(new \Lcobucci\Clock\SystemClock(new \DateTimeZone('UTC'))),
            ];

            $this->config->validator()->assert($parsedToken, ...$constraints);

            return [
                'user_id' => $parsedToken->claims()->get('user_id'),
                'email' => $parsedToken->claims()->get('email'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}