<?php

namespace App\Command;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init',
    description: 'Crée le compte administrateur et initialise les projets du portfolio.',
)]
class AppInitCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly ProjectRepository $projects,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email du compte administrateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe du compte administrateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error(sprintf('Adresse email invalide : "%s".', $email));

            return Command::FAILURE;
        }

        if (!$this->isStrongPassword($password)) {
            $io->error('Mot de passe trop faible. Il doit contenir au moins 10 caractères, avec une majuscule, une minuscule, un chiffre et un caractère spécial.');

            return Command::FAILURE;
        }

        $this->createAdmin($io, $email, $password);
        $this->seedProjects($io);

        return Command::SUCCESS;
    }

    /**
     * Politique de mot de passe fort : 10 caractères minimum, majuscule,
     * minuscule, chiffre et caractère spécial.
     */
    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 10
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/\d/', $password) === 1
            && preg_match('/[^A-Za-z0-9]/', $password) === 1;
    }

    private function createAdmin(SymfonyStyle $io, string $email, string $plainPassword): void
    {
        $user = $this->users->findOneBy(['email' => $email]);

        if ($user) {
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            $this->em->flush();
            $io->success(sprintf('Le mot de passe de l\'administrateur "%s" a été réinitialisé.', $email));

            return;
        }

        $user = (new User())
            ->setEmail($email)
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Administrateur créé : %s / %s', $email, $plainPassword));
    }

    private function seedProjects(SymfonyStyle $io): void
    {
        if (count($this->projects->findAll()) > 0) {
            return;
        }

        $seeds = [
            (new Project())
                ->setTitle('Digitalisation du Greffe Central — Cour Suprême du Bénin')
                ->setCategory('web')
                ->setDescription("Conception et développement d'une plateforme de digitalisation du rôle du Greffe Central de la Cour Suprême du Bénin, réalisée dans le cadre de mon stage académique pour l'obtention de ma licence : gestion des dossiers, enregistrement des requêtes et suivi des actes du greffe.")
                ->setTechnologies(['Symfony', 'Twig', 'Doctrine', 'Bootstrap'])
                ->setIcon('bi-bank')
                ->setFeatured(true)
                ->setPosition(1),
            (new Project())
                ->setTitle('Bibliothèque de Gestion des Livres')
                ->setCategory('web')
                ->setDescription("Application web de gestion d'une bibliothèque développée lors de mon stage académique chez DigiWeb (Porto-Novo) : catalogage des ouvrages, gestion des emprunts, des retours et des adhérents.")
                ->setTechnologies(['Python', 'Django', 'SQLite', 'Bootstrap'])
                ->setIcon('bi-book-half')
                ->setPosition(2),
            (new Project())
                ->setTitle('Portfolio Dynamique')
                ->setCategory('web')
                ->setDescription("Portfolio personnel dynamique avec espace d'administration intégré : présentation de mes compétences, parcours et projets, formulaire de contact et gestion complète des contenus.")
                ->setTechnologies(['HTML5', 'CSS3', 'JavaScript', 'Bootstrap', 'Symfony'])
                ->setIcon('bi-globe2')
                ->setPosition(3),
            (new Project())
                ->setTitle("Montage & Configuration d'Antennes Réseaux")
                ->setCategory('network')
                ->setDescription("Montage d'antennes réseaux telles que « Confass » et « Nano », leur configuration ainsi que la génération des tickets de vente de connexion internet.")
                ->setTechnologies(['Cisco Packet Tracer', 'TCP/IP', 'Câblage'])
                ->setIcon('bi-broadcast')
                ->setPosition(4),
        ];

        foreach ($seeds as $project) {
            $this->em->persist($project);
        }
        $this->em->flush();

        $io->success(count($seeds) . ' projets initiaux ont été créés.');
    }
}
