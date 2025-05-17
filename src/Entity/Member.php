<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member extends User
{
    #[ORM\ManyToOne(inversedBy: 'membersCollection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Club $club = null;

    #[ORM\Column(length: 255)]
    private ?string $clubRole = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $joinAt = null;



    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): static
    {
        $this->club = $club;

        return $this;
    }

    public function getClubRole(): ?string
    {
        return $this->clubRole;
    }

    public function setClubRole(string $clubRole): static
    {
        $this->clubRole = $clubRole;

        return $this;
    }

    public function getJoinAt(): ?\DateTimeInterface
    {
        return $this->joinAt;
    }

    public function setJoinAt(\DateTimeInterface $joinAt): static
    {
        $this->joinAt = $joinAt;

        return $this;
    }

    public function isManager(): bool
    {
        return $this->getClubRole() === 'manager'; // Adjust this condition based on your role logic
    }
}
