<?php

namespace App\Entity;

use App\Repository\EducationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EducationRepository::class)]
class Education
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $degree = null;

    #[ORM\Column(length: 255)]
    private ?string $school = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startYear = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endYear = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?FreelancerProfile $freelancerProfile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(string $degree): static
    {
        $this->degree = $degree;

        return $this;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(string $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getStartYear(): ?\DateTimeInterface
    {
        return $this->startYear;
    }

    public function setStartYear(\DateTimeInterface $startYear): static
    {
        $this->startYear = $startYear;

        return $this;
    }

    public function getEndYear(): ?\DateTimeInterface
    {
        return $this->endYear;
    }

    public function setEndYear(\DateTimeInterface $endYear): static
    {
        $this->endYear = $endYear;

        return $this;
    }

    public function getFreelancerProfile(): ?FreelancerProfile
    {
        return $this->freelancerProfile;
    }

    public function setFreelancerProfile(?FreelancerProfile $freelancerProfile): static
    {
        $this->freelancerProfile = $freelancerProfile;

        return $this;
    }

    public function update(array $date){
        if (isset($data['Degree'])) {
            $this->setDegree($data['Degree']);
        }

        if (isset($data['School'])){
            $this->setSchool($data['School']);
        }

        if (isset($date['StartYear'])){
            $this->setStartYear($date['StartYear']);
        }

        if (isset($date['EndYear'])){
            $this->setEndYear($date['EndYear']);
        }

        return $this;
    }
}
