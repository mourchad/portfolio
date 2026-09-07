<?php

namespace App\Controller\Admin;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\MediaRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/profil', name: 'admin_profile_')]
#[IsGranted('ROLE_ADMIN')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly MediaRepository $mediaRepository,
    ) {
    }

    #[Route('', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProfileRepository $repository, EntityManagerInterface $em): Response
    {
        $profile = $repository->findMain();
        if (null === $profile) {
            $profile = $repository->createMain();
        }

        $form = $this->createForm(ProfileType::class, $profile, [
            'photo_choices' => $this->buildMediaChoices('image', $profile->getPhotoPath()),
            'cv_choices' => $this->buildMediaChoices('document', $profile->getCvPath()),
        ]);
        $form->get('typedRolesRaw')->setData(implode("\n", $profile->getTypedRoles()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw = (string) $form->get('typedRolesRaw')->getData();
            $profile->setTypedRoles(
                array_values(array_filter(array_map('trim', explode("\n", $raw)), static fn ($t) => '' !== $t))
            );

            $profile->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Profil et coordonnées mis à jour.');

            return $this->redirectToRoute('admin_profile_edit');
        }

        return $this->render('admin/profile_edit.html.twig', [
            'form' => $form,
            'profile' => $profile,
        ]);
    }

    /**
     * Construit la liste [nom du fichier => chemin] de la médiathèque filtrée par nature.
     * La valeur actuellement enregistrée est toujours proposée, même absente de la médiathèque.
     *
     * @return array<string, string>
     */
    private function buildMediaChoices(string $kind, ?string $current): array
    {
        $choices = [];

        foreach ($this->mediaRepository->findAllRecent() as $media) {
            if ($media->getKind() === $kind) {
                $choices[$media->getOriginalName()] = $media->getPath();
            }
        }

        if ($current && !in_array($current, $choices, true)) {
            $choices = ['Valeur actuelle : ' . basename($current) => $current] + $choices;
        }

        return $choices;
    }
}
