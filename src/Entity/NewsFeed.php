<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\NewsFeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsFeedRepository::class)
 */
class NewsFeed
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $contentHTML;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contentMessage = "";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $socialMedia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shareLink;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=NewsFeedLike::class, mappedBy="newsfeed")
     */
    private $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentHTML(): ?string
    {
        return $this->contentHTML;
    }

    public function setContentHTML(string $contentHTML): self
    {
        $this->contentHTML = $contentHTML;

        return $this;
    }

    public function getContentMessage(): ?string
    {
        return $this->contentMessage;
    }

    public function setContentMessage(string $contentMessage): self
    {
        $this->contentMessage = $contentMessage;

        return $this;
    }

    public function getShareLink(): ?string
    {
        return $this->shareLink;
    }

    public function setShareLink(string $shareLink): self
    {
        $this->shareLink = $shareLink;

        return $this;
    }

    public function getSocialMedia(): ?string
    {
        return $this->socialMedia;
    }

    public function setSocialMedia(string $socialMedia): self
    {
        $this->socialMedia = $socialMedia;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    /**
     * @return Collection<int, NewsFeedLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(NewsFeedLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setNewsfeed($this);
        }

        return $this;
    }

    public function removeLike(NewsFeedLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getNewsfeed() === $this) {
                $like->setNewsfeed(null);
            }
        }

        return $this;
    }

    /**
     *  Permet de savoir si ce poste est liké par un utilisateur
     * 
     * @param User $user
     * @return boolean
     */
    public function isLikedByUser(User $user) : bool {
        foreach($this->likes as $like){
            if($like->getUser() === $user)
                return true;
        }
        
        return false;
    }
}
