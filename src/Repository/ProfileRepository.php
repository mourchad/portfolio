<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profile>
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * Récupère le profil principal (premier profil créé).
     * Retourne null si aucun profil n'existe.
     */
    public function findMain(): ?Profile
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }

    /**
     * Crée et retourne un nouveau profil principal si aucun n'existe.
     */
    public function createMain(): Profile
    {
        $profile = new Profile();
        $profile->setEmail(Profile::DEFAULT_EMAIL);
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->flush();

        return $profile;
    }
}
