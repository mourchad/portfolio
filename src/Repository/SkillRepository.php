<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skill>
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    /**
     * Compétences groupées par section du site public.
     *
     * @return array{competence: Skill[], expertise: Skill[], outil: Skill[], langue: Skill[]}
     */
    public function findAllGrouped(): array
    {
        $grouped = [
            'competence' => [],
            'expertise' => [],
            'outil' => [],
            'langue' => [],
        ];

        foreach ($this->findBy([], ['position' => 'ASC']) as $skill) {
            $grouped[$skill->getSkillGroup()][] = $skill;
        }

        return $grouped;
    }

    /**
     * Nombre d'étiquettes techniques distinctes (compteur « Technologies maîtrisées »).
     */
    public function countUniqueTags(): int
    {
        $tags = [];

        foreach ($this->findAllGrouped()['competence'] as $skill) {
            foreach ($skill->getTags() as $tag) {
                $tags[mb_strtolower(trim($tag))] = true;
            }
        }

        return count($tags);
    }
}
