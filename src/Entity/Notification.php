<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $notified = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $seen = null;

    #[ORM\OneToOne(inversedBy: 'notification', cascade: ['persist', 'remove'])]
    private ?Like $notifLike = null;

    #[ORM\OneToOne(inversedBy: 'notification', cascade: ['persist', 'remove'])]
    private ?Message $notifMessage = null;



    #[ORM\OneToOne(inversedBy: 'notification', cascade: ['persist', 'remove'])]
    private ?FriendRequest $notifFriendRequest = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Post $notifPost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotified(): ?Profile
    {
        return $this->notified;
    }

    public function setNotified(?Profile $notified): static
    {
        $this->notified = $notified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): static
    {
        $this->seen = $seen;

        return $this;
    }

    public function getNotifLike(): ?Like
    {
        return $this->notifLike;
    }

    public function setNotifLike(?Like $notifLike): static
    {
        $this->notifLike = $notifLike;

        return $this;
    }

    public function getNotifMessage(): ?Message
    {
        return $this->notifMessage;
    }

    public function setNotifMessage(?Message $notifMessage): static
    {
        $this->notifMessage = $notifMessage;

        return $this;
    }

    public function getNotifPost(): ?Post
    {
        return $this->notifPost;
    }

    public function setNotifPost(?Post $notifPost): static
    {
        $this->notifPost = $notifPost;

        return $this;
    }

    public function getNotifFriendRequest(): ?FriendRequest
    {
        return $this->notifFriendRequest;
    }

    public function setNotifFriendRequest(?FriendRequest $notifFriendRequest): static
    {
        $this->notifFriendRequest = $notifFriendRequest;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }
}
