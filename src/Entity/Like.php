<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Post $post = null;

    #[ORM\OneToOne(mappedBy: 'notifLike', cascade: ['persist', 'remove'])]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

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
            $this->notification->setNotifLike(null);
        }

        // set the owning side of the relation if necessary
        if ($notification !== null && $notification->getNotifLike() !== $this) {
            $notification->setNotifLike($this);
        }

        $this->notification = $notification;

        return $this;
    }
}
