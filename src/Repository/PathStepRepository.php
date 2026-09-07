<?php

namespace App\Repository;

use App\Entity\PathStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PathStep>
 */
class PathStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PathStep::class);
    }

    /**
     * Étapes ordonnées pour la frise chronologique du site public.
     *
     * @return PathStep[]
     */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['position' => 'ASC', 'year' => 'DESC']);
    }

    /**
     * Nombre d'étapes d'un type donné (ex. stages académiques du hero).
     */
    public function countByType(string $type): int
    {
        return (int) $this->count(['type' => $type]);
    }
}
