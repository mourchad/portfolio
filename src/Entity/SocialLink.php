<?php

namespace App\Entity;

use App\Repository\SocialLinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SocialLinkRepository::class)]
class SocialLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Nom affiché en aria-label (ex. « LinkedIn »). */
    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $url = null;

    /** Classe d'icône Bootstrap Icons (ex. bi-linkedin). */
    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    private ?string $icon = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\Range(min: 0, max: 9999)]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

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
