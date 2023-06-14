<?php

namespace App\Entity;

use App\Repository\FreelancerProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FreelancerProfileRepository::class)]
class FreelancerProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $skills = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $portfolio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: 'integer')]
    private ?int $userId = null;

    #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'profile')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'freelancerProfile', targetEntity: Education::class)]
    private Collection $education;

    #[ORM\OneToMany(mappedBy: 'freelanceprofile', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->education = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(string $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    public function getPortfolio(): ?string
    {
        return $this->portfolio;
    }

    public function setPortfolio(?string $portfolio): static
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Education>
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(Education $education): static
    {
        if (!$this->education->contains($education)) {
            $this->education->add($education);
            $education->setFreelancerProfile($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): static
    {
        if ($this->education->removeElement($education)) {
            // set the owning side to null (unless already changed)
            if ($education->getFreelancerProfile() === $this) {
                $education->setFreelancerProfile(null);
            }
        }

        return $this;
    }

    public function update(array $date){
        if (isset($data['Skills'])) {
            $this->setSkills($data['Skills']);
        }

        if (isset($data['Portfolio'])){
            $this->setPortfolio($data['Portfolio']);
        }

        if (isset($date['Price'])){
            $this->setPrice($date['Price']);
        }

        if (isset($date['Description'])){
            $this->setDescription($date['Description']);
        }

        if (isset($date['UserId'])){
            $this->setUserId($date['UserId']);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFreelanceprofile($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFreelanceprofile() === $this) {
                $comment->setFreelanceprofile(null);
            }
        }

        return $this;
    }
}
