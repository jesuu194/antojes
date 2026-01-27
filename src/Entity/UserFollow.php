<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_follow')]
#[ORM\UniqueConstraint(name: 'unique_follow', columns: ['follower_user_id', 'followed_user_id'])]
class UserFollow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $followerUser;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $followedUser;

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

    public function getFollowerUser(): User
    {
        return $this->followerUser;
    }

    public function setFollowerUser(User $followerUser): self
    {
        $this->followerUser = $followerUser;
        return $this;
    }

    public function getFollowedUser(): User
    {
        return $this->followedUser;
    }

    public function setFollowedUser(User $followedUser): self
    {
        $this->followedUser = $followedUser;
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