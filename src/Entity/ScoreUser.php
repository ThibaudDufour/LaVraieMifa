<?php

namespace App\Entity;

use App\Repository\ScoreUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreUserRepository::class)
 */
class ScoreUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="scoreUser", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $snake;

    /**
     * @ORM\Column(type="integer")
     */
    private $tetris;

    /**
     * @ORM\Column(type="integer")
     */
    private $game2048;

    /**
     * @ORM\Column(type="integer")
     */
    private $flappyBird;

    /**
     * @ORM\Column(type="integer")
     */
    private $spaceInvaders;

    /**
     * @ORM\Column(type="integer")
     */
    private $bubbleShooter;

    /**
     * @ORM\Column(type="integer")
     */
    private $jurassicPark;

    /**
     * @ORM\Column(type="integer")
     */
    private $doodleJump;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSnake(): ?int
    {
        return $this->snake;
    }

    public function setSnake(int $snake): self
    {
        $this->snake = $snake;

        return $this;
    }

    public function getTetris(): ?int
    {
        return $this->tetris;
    }

    public function setTetris(int $tetris): self
    {
        $this->tetris = $tetris;

        return $this;
    }

    public function getGame2048(): ?int
    {
        return $this->game2048;
    }

    public function setGame2048(int $game2048): self
    {
        $this->game2048 = $game2048;

        return $this;
    }

    public function getFlappyBird(): ?int
    {
        return $this->flappyBird;
    }

    public function setFlappyBird(int $flappyBird): self
    {
        $this->flappyBird = $flappyBird;

        return $this;
    }

    public function getSpaceInvaders(): ?int
    {
        return $this->spaceInvaders;
    }

    public function setSpaceInvaders(int $spaceInvaders): self
    {
        $this->spaceInvaders = $spaceInvaders;

        return $this;
    }

    public function getBubbleShooter(): ?int
    {
        return $this->bubbleShooter;
    }

    public function setBubbleShooter(int $bubbleShooter): self
    {
        $this->bubbleShooter = $bubbleShooter;

        return $this;
    }

    public function getJurassicPark(): ?int
    {
        return $this->jurassicPark;
    }

    public function setJurassicPark(int $jurassicPark): self
    {
        $this->jurassicPark = $jurassicPark;

        return $this;
    }

    public function getDoodleJump(): ?int
    {
        return $this->doodleJump;
    }

    public function setDoodleJump(int $doodleJump): self
    {
        $this->doodleJump = $doodleJump;

        return $this;
    }
}
