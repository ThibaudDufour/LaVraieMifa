<?php

namespace App\Entity;

use App\Repository\PodcastRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PodcastRepository::class)
 */
class Podcast
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienYouTube;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLienYouTube(): ?string
    {
        return $this->lienYouTube;
    }

    public function setLienYouTube(string $lienYouTube): self
    {
        $this->lienYouTube = $lienYouTube;

        return $this;
    }
}
