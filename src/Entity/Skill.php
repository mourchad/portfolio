<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    /** Bloc de la section « Compétences » avec barre de progression. */
    public const GROUP_COMPETENCE = 'competence';
    /** Carte de la section « Expertise » avec liste à puces. */
    public const GROUP_EXPERTISE = 'expertise';
    /** Ligne simple de la section « Logiciels ». */
    public const GROUP_OUTIL = 'outil';
    /** Entrée de la liste des langues. */
    public const GROUP_LANGUE = 'langue';

    public const GROUPS = [
        'Compétence (bloc avec progression)' => self::GROUP_COMPETENCE,
        'Expertise (carte avec points)' => self::GROUP_EXPERTISE,
        'Logiciel / outil' => self::GROUP_OUTIL,
        'Langue' => self::GROUP_LANGUE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::GROUP_COMPETENCE, self::GROUP_EXPERTISE, self::GROUP_OUTIL, self::GROUP_LANGUE])]
    private ?string $skillGroup = null;

    /** Niveau en pourcentage : barre pour les blocs/outils, pastille pour les langues. */
    #[ORM\Column(options: ['default' => 0])]
    #[Assert\Range(min: 0, max: 100)]
    private int $percent = 0;

    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(max: 60)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 600)]
    private ?string $description = null;

    /** Petites étiquettes affichées sous le titre (blocs et cartes). */
    #[ORM\Column]
    private array $tags = [];

    /** Points de la liste à cocher des cartes d'expertise. */
    #[ORM\Column]
    private array $items = [];

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\Range(min: 0, max: 9999)]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSkillGroup(): ?string
    {
        return $this->skillGroup;
    }

    public function setSkillGroup(string $skillGroup): static
    {
        $this->skillGroup = $skillGroup;

        return $this;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): static
    {
        $this->percent = $percent;

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

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): static
    {
        $this->items = $items;

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
}
