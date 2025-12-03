<?php

namespace App\Entity;

use App\Repository\TourRepository;
use App\Validator\DateRange;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: TourRepository::class)]
#[DateRange]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tour:read'])] //added
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['tour:read', 'tour:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    #[Groups(['tour:read', 'tour:write'])]
    private ?string $country = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La date de début est obligatoire")]
    #[Groups(['tour:read', 'tour:write'])]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La date de fin est obligatoire")]
    #[Groups(['tour:read', 'tour:write'])]
    private ?\DateTime $endDate = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    #[Groups(['tour:read', 'tour:write'])]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Calcule la durée du voyage en jours
     */
    public function getDuration(): int
    {
        if (!$this->startDate || !$this->endDate) {
            return 0;
        }

        $interval = $this->startDate->diff($this->endDate);
        return $interval->days;
    }
}
