<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'friend_request')]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $senderUser;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $receiverUser;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status; // 'pending', 'accepted', 'rejected', 'cancelled'

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $respondedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderUser(): User
    {
        return $this->senderUser;
    }

    public function setSenderUser(User $senderUser): self
    {
        $this->senderUser = $senderUser;
        return $this;
    }

    public function getReceiverUser(): User
    {
        return $this->receiverUser;
    }

    public function setReceiverUser(User $receiverUser): self
    {
        $this->receiverUser = $receiverUser;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getRespondedAt(): ?\DateTime
    {
        return $this->respondedAt;
    }

    public function setRespondedAt(?\DateTime $respondedAt): self
    {
        $this->respondedAt = $respondedAt;
        return $this;
    }
}