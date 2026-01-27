<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_block')]
#[ORM\UniqueConstraint(name: 'unique_block', columns: ['blocker_user_id', 'blocked_user_id'])]
class UserBlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $blockerUser;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $blockedUser;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlockerUser(): User
    {
        return $this->blockerUser;
    }

    public function setBlockerUser(User $blockerUser): self
    {
        $this->blockerUser = $blockerUser;
        return $this;
    }

    public function getBlockedUser(): User
    {
        return $this->blockedUser;
    }

    public function setBlockedUser(User $blockedUser): self
    {
        $this->blockedUser = $blockedUser;
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
}