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
        // Usuarios de Valencia con coordenadas reales
        // Centro de Valencia: 39.4699° N, 0.3763° W
        
        $valenciaUsers = [
            ['name' => 'María García', 'email' => 'maria.garcia@valencia.com', 'lat' => 39.4699, 'lng' => -0.3763, 'online' => true], // Plaza del Ayuntamiento
            ['name' => 'Carlos Martínez', 'email' => 'carlos.martinez@valencia.com', 'lat' => 39.4702, 'lng' => -0.3768, 'online' => true], // Cerca del Ayuntamiento
            ['name' => 'Ana López', 'email' => 'ana.lopez@valencia.com', 'lat' => 39.4754, 'lng' => -0.3773, 'online' => true], // Torres de Serranos
            ['name' => 'David Rodríguez', 'email' => 'david.rodriguez@valencia.com', 'lat' => 39.4845, 'lng' => -0.3472, 'online' => true], // Ciudad de las Artes y las Ciencias
            ['name' => 'Laura Fernández', 'email' => 'laura.fernandez@valencia.com', 'lat' => 39.4628, 'lng' => -0.3758, 'online' => true], // Estación del Norte
            ['name' => 'Javier Sánchez', 'email' => 'javier.sanchez@valencia.com', 'lat' => 39.4747, 'lng' => -0.3758, 'online' => true], // Barrio del Carmen
            ['name' => 'Elena Gómez', 'email' => 'elena.gomez@valencia.com', 'lat' => 39.4562, 'lng' => -0.3518, 'online' => true], // Malvarrosa
            ['name' => 'Miguel Díaz', 'email' => 'miguel.diaz@valencia.com', 'lat' => 39.4821, 'lng' => -0.3647, 'online' => true], // Jardines del Turia
            ['name' => 'Isabel Ruiz', 'email' => 'isabel.ruiz@valencia.com', 'lat' => 39.4673, 'lng' => -0.3812, 'online' => true], // Mercado Central
            ['name' => 'Antonio Moreno', 'email' => 'antonio.moreno@valencia.com', 'lat' => 39.4695, 'lng' => -0.3801, 'online' => true], // Lonja de la Seda
            ['name' => 'Carmen Jiménez', 'email' => 'carmen.jimenez@valencia.com', 'lat' => 39.4756, 'lng' => -0.3692, 'online' => true], // Plaza de la Virgen
            ['name' => 'Francisco Álvarez', 'email' => 'francisco.alvarez@valencia.com', 'lat' => 39.4612, 'lng' => -0.3894, 'online' => true], // Mestalla
            ['name' => 'Lucía Romero', 'email' => 'lucia.romero@valencia.com', 'lat' => 39.4781, 'lng' => -0.3589, 'online' => true], // Bioparc
            ['name' => 'Pablo Torres', 'email' => 'pablo.torres@valencia.com', 'lat' => 39.4658, 'lng' => -0.3521, 'online' => true], // Puerto de Valencia
            ['name' => 'Marta Navarro', 'email' => 'marta.navarro@valencia.com', 'lat' => 39.4715, 'lng' => -0.3745, 'online' => true], // Catedral
            ['name' => 'Raúl Domínguez', 'email' => 'raul.dominguez@valencia.com', 'lat' => 39.4692, 'lng' => -0.3623, 'online' => true], // Antiguo cauce del Turia
            ['name' => 'Sara Vázquez', 'email' => 'sara.vazquez@valencia.com', 'lat' => 39.4835, 'lng' => -0.3512, 'online' => true], // Oceanográfico
            ['name' => 'Alberto Gil', 'email' => 'alberto.gil@valencia.com', 'lat' => 39.4643, 'lng' => -0.3682, 'online' => true], // Russafa
            ['name' => 'Patricia Serrano', 'email' => 'patricia.serrano@valencia.com', 'lat' => 39.4598, 'lng' => -0.3612, 'online' => true], // Grao
            ['name' => 'Daniel Castro', 'email' => 'daniel.castro@valencia.com', 'lat' => 39.4776, 'lng' => -0.3721, 'online' => true], // Torres de Quart
        ];

        $users = [];
        foreach ($valenciaUsers as $index => $userData) {
            $user = new User();
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setLat($userData['lat']);
            $user->setLng($userData['lng']);
            $user->setOnline($userData['online']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Crear usuario de prueba adicional
        $testUser = new User();
        $testUser->setName('Test User');
        $testUser->setEmail('test@example.com');
        $testUser->setPassword($this->passwordHasher->hashPassword($testUser, 'password123'));
        $testUser->setLat(39.4699);
        $testUser->setLng(-0.3763);
        $testUser->setOnline(true);
        $manager->persist($testUser);
        $users[] = $testUser;

        $user1 = $users[0];
        $user2 = $users[1];
        $user3 = $users[2];

        $manager->flush();

        // Create general chat with id = 1
        $generalChat = new Chat();
        $generalChat->setId(1);
        $generalChat->setType('general');
        $generalChat->setIsActive(true);
        $manager->persist($generalChat);

        $manager->flush();

        // Add first 4 users to general chat
        foreach (array_slice($users, 0, 4) as $user) {
            $member = new ChatMember();
            $member->setChat($generalChat);
            $member->setUser($user);
            $manager->persist($member);
        }

        // Create sample messages in general chat
        $msg1 = new Message();
        $msg1->setChat($generalChat);
        $msg1->setUser($users[0]);
        $msg1->setText('¡Hola a todos!');
        $manager->persist($msg1);

        $msg2 = new Message();
        $msg2->setChat($generalChat);
        $msg2->setUser($users[1]);
        $msg2->setText('¡Hola!');
        $manager->persist($msg2);

        // Create private chat between user1 and user2
        $privateChat = new Chat();
        $privateChat->setType('private');
        $privateChat->setIsActive(true);
        $manager->persist($privateChat);

        $member1 = new ChatMember();
        $member1->setChat($privateChat);
        $member1->setUser($users[0]);
        $manager->persist($member1);

        $member2 = new ChatMember();
        $member2->setChat($privateChat);
        $member2->setUser($users[1]);
        $manager->persist($member2);

        // Add messages to private chat
        $pvtMsg1 = new Message();
        $pvtMsg1->setChat($privateChat);
        $pvtMsg1->setUser($users[0]);
        $pvtMsg1->setText('This is a private message');
        $manager->persist($pvtMsg1);

        // Social relationships
        // User 0 follows User 1
        $follow1 = new UserFollow();
        $follow1->setFollowerUser($users[0]);
        $follow1->setFollowedUser($users[1]);
        $manager->persist($follow1);

        // User 1 follows User 2
        $follow2 = new UserFollow();
        $follow2->setFollowerUser($users[1]);
        $follow2->setFollowedUser($users[2]);
        $manager->persist($follow2);

        // User 0 and User 2 are friends (friend request accepted)
        $friendReq = new FriendRequest();
        $friendReq->setSenderUser($users[0]);
        $friendReq->setReceiverUser($users[2]);
        $friendReq->setStatus('accepted');
        $friendReq->setRespondedAt(new \DateTime());
        $manager->persist($friendReq);

        // User 1 has a pending friend request from User 3
        $pendingReq = new FriendRequest();
        $pendingReq->setSenderUser($users[3]);
        $pendingReq->setReceiverUser($users[1]);
        $pendingReq->setStatus('pending');
        $manager->persist($pendingReq);

        // User 2 blocked User 3
        $block = new UserBlock();
        $block->setBlockerUser($users[2]);
        $block->setBlockedUser($users[3]);
        $manager->persist($block);

        $manager->flush();
    }
}
