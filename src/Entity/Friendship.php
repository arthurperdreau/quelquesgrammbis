<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
class Friendship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friendAsPersonA')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $personA = null;

    #[ORM\ManyToOne(inversedBy: 'friendAsPersonB')]
    private ?Profile $personB = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonA(): ?Profile
    {
        return $this->personA;
    }

    public function setPersonA(?Profile $personA): static
    {
        $this->personA = $personA;

        return $this;
    }

    public function getPersonB(): ?Profile
    {
        return $this->personB;
    }

    public function setPersonB(?Profile $personB): static
    {
        $this->personB = $personB;

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
}
