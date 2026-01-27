<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create general chat
        $generalChat = new Chat();
        $generalChat->setId(1);
        $generalChat->setType('general');
        $generalChat->setIsActive(true);
        $manager->persist($generalChat);

        // Create test users
        $user1 = new User();
        $user1->setName('User One');
        $user1->setEmail('user1@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password'));
        $user1->setLat(40.7128);
        $user1->setLng(-74.0060);
        $user1->setOnline(false);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('User Two');
        $user2->setEmail('user2@example.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
        $user2->setLat(40.7129);
        $user2->setLng(-74.0061);
        $user2->setOnline(false);
        $manager->persist($user2);

        $manager->flush();
    }
}
