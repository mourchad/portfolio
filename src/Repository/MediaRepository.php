<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * @return Media[] Tous les médias, du plus récent au plus ancien.
     */
    public function findAllRecent(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Media[] Uniquement les images (utilisables comme visuels de projets).
     */
    public function findImages(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.kind = :kind')
            ->setParameter('kind', Media::KIND_IMAGE)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
