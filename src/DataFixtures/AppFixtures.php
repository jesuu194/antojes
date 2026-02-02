<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\ChatMember;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\UserBlock;
use App\Entity\UserFollow;
use App\Entity\FriendRequest;
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

        $user3 = new User();
        $user3->setName('User Three');
        $user3->setEmail('user3@example.com');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'password'));
        $user3->setLat(40.7130);
        $user3->setLng(-74.0062);
        $user3->setOnline(false);
        $manager->persist($user3);

        $user4 = new User();
        $user4->setName('User Four');
        $user4->setEmail('user4@example.com');
        $user4->setPassword($this->passwordHasher->hashPassword($user4, 'password'));
        $user4->setLat(41.0000); // Outside 5km radius
        $user4->setLng(-75.0000);
        $user4->setOnline(false);
        $manager->persist($user4);

        $manager->flush();

        // Create general chat with id = 1
        $generalChat = new Chat();
        $generalChat->setId(1);
        $generalChat->setType('general');
        $generalChat->setIsActive(true);
        $manager->persist($generalChat);

        $manager->flush();

        // Add users to general chat
        foreach ([$user1, $user2, $user3, $user4] as $user) {
            $member = new ChatMember();
            $member->setChat($generalChat);
            $member->setUser($user);
            $manager->persist($member);
        }

        // Create sample messages in general chat
        $msg1 = new Message();
        $msg1->setChat($generalChat);
        $msg1->setUser($user1);
        $msg1->setText('Hello everyone!');
        $manager->persist($msg1);

        $msg2 = new Message();
        $msg2->setChat($generalChat);
        $msg2->setUser($user2);
        $msg2->setText('Hi there!');
        $manager->persist($msg2);

        // Create private chat between user1 and user2
        $privateChat = new Chat();
        $privateChat->setType('private');
        $privateChat->setIsActive(true);
        $manager->persist($privateChat);

        $member1 = new ChatMember();
        $member1->setChat($privateChat);
        $member1->setUser($user1);
        $manager->persist($member1);

        $member2 = new ChatMember();
        $member2->setChat($privateChat);
        $member2->setUser($user2);
        $manager->persist($member2);

        // Add messages to private chat
        $pvtMsg1 = new Message();
        $pvtMsg1->setChat($privateChat);
        $pvtMsg1->setUser($user1);
        $pvtMsg1->setText('This is a private message');
        $manager->persist($pvtMsg1);

        // Social relationships
        // User1 follows User2
        $follow1 = new UserFollow();
        $follow1->setFollowerUser($user1);
        $follow1->setFollowedUser($user2);
        $manager->persist($follow1);

        // User2 follows User3
        $follow2 = new UserFollow();
        $follow2->setFollowerUser($user2);
        $follow2->setFollowedUser($user3);
        $manager->persist($follow2);

        // User1 and User3 are friends (friend request accepted)
        $friendReq = new FriendRequest();
        $friendReq->setSenderUser($user1);
        $friendReq->setReceiverUser($user3);
        $friendReq->setStatus('accepted');
        $friendReq->setRespondedAt(new \DateTime());
        $manager->persist($friendReq);

        // User2 has a pending friend request from User4
        $pendingReq = new FriendRequest();
        $pendingReq->setSenderUser($user4);
        $pendingReq->setReceiverUser($user2);
        $pendingReq->setStatus('pending');
        $manager->persist($pendingReq);

        // User3 blocked User4
        $block = new UserBlock();
        $block->setBlockerUser($user3);
        $block->setBlockedUser($user4);
        $manager->persist($block);

        $manager->flush();
    }
}
