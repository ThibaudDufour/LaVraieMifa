<?php

namespace App\Entity;

use App\Repository\MemberProfilRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberProfilRepository::class)
 */
class MemberProfil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="memberProfil", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $spotifyUrl;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $orientation;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $reseaux = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getSpotifyUrl(): ?string
    {
        return $this->spotifyUrl;
    }

    public function setSpotifyUrl(?string $spotifyUrl): self
    {
        $this->spotifyUrl = $spotifyUrl;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getReseaux(): ?array
    {
        return $this->reseaux;
    }

    public function setReseaux(?array $reseaux): self
    {
        $this->reseaux = $reseaux;

        return $this;
    }
}
