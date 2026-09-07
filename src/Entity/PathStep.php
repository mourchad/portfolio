<?php

namespace App\Entity;

use App\Repository\PathStepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PathStepRepository::class)]
class PathStep
{
    public const TYPE_STAGE = 'stage';
    public const TYPE_FORMATION = 'formation';
    public const TYPE_DIPLOME = 'diplome';
    public const TYPE_CERTIFICAT = 'certificat';
    public const TYPE_EXPERIENCE = 'experience';

    public const TYPES = [
        'Stage' => self::TYPE_STAGE,
        'Formation' => self::TYPE_FORMATION,
        'Diplôme' => self::TYPE_DIPLOME,
        'Certificat' => self::TYPE_CERTIFICAT,
        'Expérience' => self::TYPE_EXPERIENCE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 12)]
    private ?string $year = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $dateLabel = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    private ?string $title = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private ?string $type = null;

    #[ORM\Column(length: 160, nullable: true)]
    #[Assert\Length(max: 160)]
    private ?string $place = null;

    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(max: 60)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1200)]
    private ?string $description = null;

    #[ORM\Column]
    private array $tags = [];

    #[ORM\Column(options: ['default' => false])]
    private bool $featured = false;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\Range(min: 0, max: 9999)]
    private int $position = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getDateLabel(): ?string
    {
        return $this->dateLabel;
    }

    public function setDateLabel(string $dateLabel): static
    {
        $this->dateLabel = $dateLabel;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
