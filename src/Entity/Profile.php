<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayname = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ofUser = null;

    #[ORM\OneToOne(inversedBy: 'profile',)]
    private ?Image $picture = null;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'personA')]
    private Collection $friendAsPersonA;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'personB')]
    private Collection $friendAsPersonB;

    /**
     * @var Collection<int, FriendRequest>
     */
    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'sender')]
    private Collection $SentFriendRequests;

    /**
     * @var Collection<int, FriendRequest>
     */
    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'recipient')]
    private Collection $receiveFriendRequests;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;


    public function __construct()
    {
        $this->friendAsPersonA = new ArrayCollection();
        $this->friendAsPersonB = new ArrayCollection();
        $this->SentFriendRequests = new ArrayCollection();
        $this->receiveFriendRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(?string $displayname): static
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getOfUser(): ?User
    {
        return $this->ofUser;
    }

    public function setOfUser(User $ofUser): static
    {
        $this->ofUser = $ofUser;

        return $this;
    }

    public function getPicture(): ?Image
    {
        return $this->picture;
    }

    public function setPicture(?Image $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendAsPersonA(): Collection
    {
        return $this->friendAsPersonA;
    }

    public function addFriendAsPersonA(Friendship $friendAsPersonA): static
    {
        if (!$this->friendAsPersonA->contains($friendAsPersonA)) {
            $this->friendAsPersonA->add($friendAsPersonA);
            $friendAsPersonA->setPersonA($this);
        }

        return $this;
    }

    public function removeFriendAsPersonA(Friendship $friendAsPersonA): static
    {
        if ($this->friendAsPersonA->removeElement($friendAsPersonA)) {
            // set the owning side to null (unless already changed)
            if ($friendAsPersonA->getPersonA() === $this) {
                $friendAsPersonA->setPersonA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendAsPersonB(): Collection
    {
        return $this->friendAsPersonB;
    }

    public function addFriendAsPersonB(Friendship $friendAsPersonB): static
    {
        if (!$this->friendAsPersonB->contains($friendAsPersonB)) {
            $this->friendAsPersonB->add($friendAsPersonB);
            $friendAsPersonB->setPersonB($this);
        }

        return $this;
    }

    public function removeFriendAsPersonB(Friendship $friendAsPersonB): static
    {
        if ($this->friendAsPersonB->removeElement($friendAsPersonB)) {
            // set the owning side to null (unless already changed)
            if ($friendAsPersonB->getPersonB() === $this) {
                $friendAsPersonB->setPersonB(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->SentFriendRequests;
    }

    public function addSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if (!$this->SentFriendRequests->contains($sentFriendRequest)) {
            $this->SentFriendRequests->add($sentFriendRequest);
            $sentFriendRequest->setSender($this);
        }

        return $this;
    }

    public function removeSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if ($this->SentFriendRequests->removeElement($sentFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($sentFriendRequest->getSender() === $this) {
                $sentFriendRequest->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getReceiveFriendRequests(): Collection
    {
        return $this->receiveFriendRequests;
    }

    public function addReceiveFriendRequest(FriendRequest $receiveFriendRequest): static
    {
        if (!$this->receiveFriendRequests->contains($receiveFriendRequest)) {
            $this->receiveFriendRequests->add($receiveFriendRequest);
            $receiveFriendRequest->setRecipient($this);
        }

        return $this;
    }

    public function removeReceiveFriendRequest(FriendRequest $receiveFriendRequest): static
    {
        if ($this->receiveFriendRequests->removeElement($receiveFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($receiveFriendRequest->getRecipient() === $this) {
                $receiveFriendRequest->setRecipient(null);
            }
        }

        return $this;
    }

    public function isPendingFriendRequest(Profile $profile): bool
    {
        foreach ($this->receiveFriendRequests as $friendRequest) {
            if ($friendRequest->getSender() === $profile) {
                return true;
            }
        }
        foreach ($this->SentFriendRequests as $friendRequest) {
            if ($friendRequest->getRecipient() === $profile) {
                return true;
            }
        }
        return false;
    }

    public function isFriendsWith(Profile $profile): bool
    {
        foreach ($this->friendAsPersonA as $friendShip) {
            if ($friendShip->getPersonB() === $profile) {
                return true;
            }
        }

        foreach ($this->friendAsPersonB as $friendShip) {
            if ($friendShip->getPersonA() === $profile) {
                return true;
            }
        }
        return false;
    }

    public function getFriends() : array
    {
        $friends = [];
        foreach ($this->friendAsPersonA as $friendShip) {
            $friends[] =$friendShip->getPersonB();
        }
        foreach ($this->friendAsPersonB as $friendShip) {
            $friends[] =$friendShip->getPersonA();
        }
        return $friends;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }


}
