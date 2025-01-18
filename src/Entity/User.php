<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    public $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    public $name;

    /**
     * @ORM\Column(type="string")
     */
    public $firstname;

    /**
     * @ORM\Column(type="boolean")
     */
    public $reciveMail = true;

    /**
     * @ORM\Column(type="string", options={"default" : "-"})
     */
    public $profilPhotoPath;

    /**
     * @ORM\Column(type="string", options={"default" : "-"})
     */
    public $functionUser = "-";

    /**
     * @ORM\Column(type="boolean")
     */
    public $isVerified = false;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreSnake = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreTetris = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $score2048 = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreFlappyBird = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scorePacMan = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreSpaceInvaders = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreBubbleShooter = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreJurassicPark = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : "0"})
     */
    public $scoreDoodleJump = 0;

    /**
     * @ORM\OneToMany(targetEntity=NewsFeedLike::class, mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToOne(targetEntity=MemberProfil::class, mappedBy="userId", cascade={"persist", "remove"})
     */
    private $memberProfil;

    /**
     * @ORM\OneToOne(targetEntity=ScoreUser::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $scoreUser;

    /**
     * @ORM\OneToMany(targetEntity=TracaMailAdmin::class, mappedBy="user")
     */
    private $tracaMailAdmins;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->tracaMailAdmins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFisrtname(string $fisrtname): self
    {
        $this->firstname = $fisrtname;

        return $this;
    }


    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getProfilPhotoPath(): ?string
    {
        return $this->profilPhotoPath;
    }

    public function setProfilPhotoPath(string $profilPhotoPath): self
    {
        $this->profilPhotoPath = $profilPhotoPath;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUserEmail(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        $username = "$this->firstname $this->name";
        return $username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getReciveMail(): bool
    {
        return $this->reciveMail;
    }

    public function setReciveMail(bool $reciveMail): self
    {
        $this->reciveMail = $reciveMail;

        return $this;
    }

    public function getScoreSnake() : int 
    {
        return $this->scoreSnake;
    }

    public function setScoreSnake(int $scoreSnake) : self
    {
        $this->scoreSnake = $scoreSnake;

        return $this;
    }

    public function getScoreTetris() : int 
    {
        return $this->scoreTetris;
    }

    public function setScoreTetris(int $scoreTetris) : self
    {
        $this->scoreTetris = $scoreTetris;

        return $this;
    }

    public function getScore2048() : int 
    {
        return $this->score2048;
    }

    public function setScore2048(int $score2048) : self
    {
        $this->score2048 = $score2048;

        return $this;
    }

    public function getScoreFlappyBird() : int 
    {
        return $this->scoreFlappyBird;
    }

    public function setScoreFlappyBird(int $scoreFlappyBird) : self
    {
        $this->scoreFlappyBird = $scoreFlappyBird;

        return $this;
    }

    public function getScorePacMan() : int 
    {
        return $this->scorePacMan;
    }

    public function setScorePacMan(int $scorePacMan) : self
    {
        $this->scorePacMan = $scorePacMan;

        return $this;
    }

    public function getScoreSpaceInvaders() : int 
    {
        return $this->scoreSpaceInvaders;
    }

    public function setScoreSpaceInvaders(int $scoreSpaceInvaders) : self
    {
        $this->scoreSpaceInvaders = $scoreSpaceInvaders;

        return $this;
    }

    public function getScoreBubbleShooter() : int 
    {
        return $this->scoreBubbleShooter;
    }

    public function setScoreBubbleShooter(int $scoreBubbleShooter) : self
    {
        $this->scoreBubbleShooter = $scoreBubbleShooter;

        return $this;
    }

    public function getScoreJurassicPark() : int 
    {
        return $this->scoreJurassicPark;
    }

    public function setScoreJurassicPark(int $scoreJurassicPark) : self
    {
        $this->scoreJurassicPark = $scoreJurassicPark;

        return $this;
    }

    public function getScoreDoodleJump() : int 
    {
        return $this->scoreDoodleJump;
    }

    public function setScoreDoodleJump(int $scoreDoodleJump) : self
    {
        $this->scoreDoodleJump = $scoreDoodleJump;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getFunctionUser() : string
    {
        return $this->functionUser;
    }

    public function setFunctionUser(string $functionUser) :self
    {
        $this->functionUser = $functionUser;

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(NewsFeedLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    public function getMemberProfil(): ?MemberProfil
    {
        return $this->memberProfil;
    }

    public function setMemberProfil(MemberProfil $memberProfil): self
    {
        // set the owning side of the relation if necessary
        if ($memberProfil->getUserId() !== $this) {
            $memberProfil->setUserId($this);
        }

        $this->memberProfil = $memberProfil;

        return $this;
    }

    public function getScoreUser(): ?ScoreUser
    {
        return $this->scoreUser;
    }

    public function setScoreUser(?ScoreUser $scoreUser): self
    {
        // unset the owning side of the relation if necessary
        if ($scoreUser === null && $this->scoreUser !== null) {
            $this->scoreUser->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($scoreUser !== null && $scoreUser->getUser() !== $this) {
            $scoreUser->setUser($this);
        }

        $this->scoreUser = $scoreUser;

        return $this;
    }

    /**
     * @return Collection<int, TracaMailAdmin>
     */
    public function getTracaMailAdmins(): Collection
    {
        return $this->tracaMailAdmins;
    }

    public function addTracaMailAdmin(TracaMailAdmin $tracaMailAdmin): self
    {
        if (!$this->tracaMailAdmins->contains($tracaMailAdmin)) {
            $this->tracaMailAdmins[] = $tracaMailAdmin;
            $tracaMailAdmin->setUser($this);
        }

        return $this;
    }

    public function removeTracaMailAdmin(TracaMailAdmin $tracaMailAdmin): self
    {
        if ($this->tracaMailAdmins->removeElement($tracaMailAdmin)) {
            // set the owning side to null (unless already changed)
            if ($tracaMailAdmin->getUser() === $this) {
                $tracaMailAdmin->setUser(null);
            }
        }

        return $this;
    }
}
