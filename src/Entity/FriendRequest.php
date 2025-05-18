<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'SentFriendRequests')]
    private ?Profile $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receiveFriendRequests')]
    private ?Profile $recipient = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'notifFriendRequest', cascade: ['persist', 'remove'])]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Profile
    {
        return $this->sender;
    }

    public function setSender(?Profile $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?Profile
    {
        return $this->recipient;
    }

    public function setRecipient(?Profile $recipient): static
    {
        $this->recipient = $recipient;

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

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        // unset the owning side of the relation if necessary
        if ($notification === null && $this->notification !== null) {
            $this->notification->setNotifFriendRequest(null);
        }

        // set the owning side of the relation if necessary
        if ($notification !== null && $notification->getNotifFriendRequest() !== $this) {
            $notification->setNotifFriendRequest($this);
        }

        $this->notification = $notification;

        return $this;
    }
}
