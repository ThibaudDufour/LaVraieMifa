<?php

namespace App\Entity;

use App\Repository\NewsFeedLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsFeedLikeRepository::class)
 */
class NewsFeedLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=NewsFeed::class, inversedBy="likes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $newsfeed;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNewsfeed(): ?NewsFeed
    {
        return $this->newsfeed;
    }

    public function setNewsfeed(?NewsFeed $newsfeed): self
    {
        $this->newsfeed = $newsfeed;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
