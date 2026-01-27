<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'chat_member')]
class ChatMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Chat::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Chat $chat;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $leftAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function setChat(Chat $chat): self
    {
        $this->chat = $chat;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getLeftAt(): ?\DateTime
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTime $leftAt): self
    {
        $this->leftAt = $leftAt;
        return $this;
    }
}