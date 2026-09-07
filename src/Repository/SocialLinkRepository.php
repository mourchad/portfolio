<?php

namespace App\Repository;

use App\Entity\SocialLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialLink>
 */
class SocialLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialLink::class);
    }

    /**
     * Liens ordonnés pour les rangées d'icônes sociales.
     *
     * @return SocialLink[]
     */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
