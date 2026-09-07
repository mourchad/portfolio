<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Réglages éditoriaux du site.
 * Note : L'ID n'est plus fixé à 1 pour éviter l'anti-pattern singleton.
 * Le repository gère la logique de récupération du profil principal.
 */
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    public const DEFAULT_EMAIL = 'omourchad@gmail.com';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Badge du hero (diplôme mis en avant). */
    #[ORM\Column(length: 200)]
    #[Assert\Length(max: 200)]
    private ?string $aboutBadge = null;

    /** Bio du hero. HTML limité toléré : champ réservé à l'administrateur. */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(max: 800)]
    private ?string $heroLead = '';

    /** Rôles de l'animation de frappe du hero. */
    #[ORM\Column]
    private array $typedRoles = [];

    /** Chemin relatif public de la photo (médiathèque). */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $photoPath = null;

    /** Chemin relatif public du CV (médiathèque, document PDF). */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $cvPath = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 40, nullable: true)]
    #[Assert\Length(max: 40)]
    private ?string $phone1 = null;

    #[ORM\Column(length: 40, nullable: true)]
    #[Assert\Length(max: 40)]
    private ?string $phone2 = null;

    #[ORM\Column(length: 200, nullable: true)]
    #[Assert\Length(max: 200)]
    private ?string $address = null;

    /** Courte bio affichée dans le pied de page. */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 400)]
    private ?string $footerBio = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $metaDescription = null;

    /** Mention affichée dans la barre de statistiques (ex. « 16/20 »). */
    #[ORM\Column(length: 40, nullable: true)]
    #[Assert\Length(max: 40)]
    private ?string $statMention = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAboutBadge(): ?string
    {
        return $this->aboutBadge;
    }

    public function setAboutBadge(?string $aboutBadge): static
    {
        $this->aboutBadge = $aboutBadge;

        return $this;
    }

    public function getHeroLead(): ?string
    {
        return $this->heroLead;
    }

    public function setHeroLead(?string $heroLead): static
    {
        $this->heroLead = $heroLead;

        return $this;
    }

    public function getTypedRoles(): array
    {
        return $this->typedRoles;
    }

    public function setTypedRoles(array $typedRoles): static
    {
        $this->typedRoles = $typedRoles;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): static
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    public function getCvPath(): ?string
    {
        return $this->cvPath;
    }

    public function setCvPath(?string $cvPath): static
    {
        $this->cvPath = $cvPath;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(?string $phone1): static
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): static
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getFooterBio(): ?string
    {
        return $this->footerBio;
    }

    public function setFooterBio(?string $footerBio): static
    {
        $this->footerBio = $footerBio;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getStatMention(): ?string
    {
        return $this->statMention;
    }

    public function setStatMention(?string $statMention): static
    {
        $this->statMention = $statMention;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
