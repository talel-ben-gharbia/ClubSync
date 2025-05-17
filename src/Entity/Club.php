<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $members = null;

    #[ORM\Column(length: 255)]
    private ?string $president = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $foundation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['Active', 'Inactive'], message: 'Invalid status value.')]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $joinRequest = null;

    /**
     * @var Collection<int, Member>
     */
    #[ORM\OneToMany(targetEntity: Member::class, mappedBy: 'club', orphanRemoval: true)]
    private Collection $membersCollection;



    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->membersCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getMembers(): ?int
    {
        return $this->members;
    }

    public function setMembers(int $members): static
    {
        $this->members = $members;
        return $this;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): static
    {
        $this->president = $president;
        return $this;
    }

    public function getFoundation(): ?\DateTimeInterface
    {
        return $this->foundation;
    }

    public function setFoundation(?\DateTimeInterface $foundation): static
    {
        $this->foundation = $foundation;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setClub($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            if ($event->getClub() === $this) {
                $event->setClub(null);
            }
        }

        return $this;
    }







    public function getJoinRequest(): ?array  // Return can be null
    {
        return $this->joinRequest;
    }

    public function setJoinRequest(?array $joinRequest): static  // Accepts null
    {
        $this->joinRequest = $joinRequest;
        return $this;
    }

    public function addJoinRequest(array $requestData): self
    {
        $joinRequests = $this->getJoinRequest();
        $joinRequests[] = $requestData;
        $this->setJoinRequest($joinRequests);
        return $this;
    }

    public function hasUserPendingRequest(User $user): bool
    {
        foreach ($this->getJoinRequest() as $request) {
            if (($request['user_id'] ?? null) == $user->getId()
                && ($request['status'] ?? null) === 'pending'
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembersCollection(): Collection
    {
        return $this->membersCollection;
    }

    public function addMembersCollection(Member $membersCollection): static
    {
        if (!$this->membersCollection->contains($membersCollection)) {
            $this->membersCollection->add($membersCollection);
            $membersCollection->setClub($this);
        }

        return $this;
    }

    public function removeMembersCollection(Member $membersCollection): static
    {
        if ($this->membersCollection->removeElement($membersCollection)) {
            // set the owning side to null (unless already changed)
            if ($membersCollection->getClub() === $this) {
                $membersCollection->setClub(null);
            }
        }

        return $this;
    }

    
}
