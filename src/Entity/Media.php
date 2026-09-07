<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    public const KIND_IMAGE = 'image';
    public const KIND_DOCUMENT = 'document';

    /**
     * Extensions autorisées, par nature de fichier. Le SVG est volontairement
     * exclu : il permet d'embarquer du JavaScript (faille XSS stockée).
     */
    public const ALLOWED = [
        self::KIND_IMAGE => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'],
        self::KIND_DOCUMENT => ['pdf', 'txt', 'csv', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods'],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du fichier tel que stocké sur le disque (nom aléatoire + extension).
     */
    #[ORM\Column(length: 80, unique: true)]
    private ?string $filename = null;

    /**
     * Nom d'origine du fichier, affiché à l'administrateur.
     */
    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column(length: 100)]
    private ?string $mimeType = null;

    /**
     * Taille en octets.
     */
    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\LessThanOrEqual(value: 5242880, message: 'Le fichier ne doit pas dépasser 5 Mo.')]
    private ?int $size = null;

    /**
     * Nature du fichier : 'image' ou 'document'.
     */
    #[ORM\Column(length: 20)]
    private ?string $kind = null;

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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): static
    {
        if (!\in_array($kind, [self::KIND_IMAGE, self::KIND_DOCUMENT], true)) {
            throw new \InvalidArgumentException('Nature de fichier inconnue.');
        }
        $this->kind = $kind;

        return $this;
    }

    public function isImage(): bool
    {
        return self::KIND_IMAGE === $this->kind;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Chemin public relatif, utilisable directement dans asset().
     */
    public function getPath(): string
    {
        return 'uploads/media/' . $this->filename;
    }
}
