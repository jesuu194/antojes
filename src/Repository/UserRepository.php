<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findNearby(float $lat, float $lng, float $radius = 5): array
    {
        // Haversine formula in SQL (approximate)
        $qb = $this->createQueryBuilder('u')
            ->where('u.lat IS NOT NULL')
            ->andWhere('u.lng IS NOT NULL');

        $users = $qb->getQuery()->getResult();
        $nearby = [];

        foreach ($users as $user) {
            $distance = $this->haversineDistance($lat, $lng, floatval($user->getLat()), floatval($user->getLng()));
            if ($distance <= $radius) {
                $nearby[] = ['user' => $user, 'distance' => $distance];
            }
        }

        return $nearby;
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
