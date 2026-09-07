<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\MediaRepository;
use App\Repository\ProjectRepository;
use App\Service\ProjectTechnologyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/projets', name: 'admin_project_')]
#[IsGranted('ROLE_ADMIN')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly ProjectTechnologyService $technologyService,
    ) {
    }

    #[Route('', name: 'index')]
    public function index(ProjectRepository $repository): Response
    {
        return $this->render('admin/projects.html.twig', [
            'projects' => $repository->findAllOrdered(),
        ]);
    }

    #[Route('/nouveau', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'image_choices' => $this->buildImageChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw = (string) $form->get('technologiesRaw')->getData();
            $project->setTechnologies($this->technologyService->convertRawToArray($raw));

            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Projet « ' . $project->getTitle() . ' » ajouté avec succès.');

            return $this->redirectToRoute('admin_project_index');
        }

        return $this->render('admin/project_form.html.twig', [
            'form' => $form,
            'project' => $project,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjectType::class, $project, [
            'image_choices' => $this->buildImageChoices($project),
        ]);
        $form->get('technologiesRaw')->setData($this->technologyService->convertArrayToRaw($project->getTechnologies()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw = (string) $form->get('technologiesRaw')->getData();
            $project->setTechnologies($this->technologyService->convertRawToArray($raw));

            $em->flush();

            $this->addFlash('success', 'Projet « ' . $project->getTitle() . ' » mis à jour.');

            return $this->redirectToRoute('admin_project_index');
        }

        return $this->render('admin/project_form.html.twig', [
            'form' => $form,
            'project' => $project,
            'isEdit' => true,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_project_' . $project->getId(), (string) $request->request->get('_token'))) {
            $em->remove($project);
            $em->flush();
            $this->addFlash('success', 'Projet supprimé.');
        }

        return $this->redirectToRoute('admin_project_index');
    }

    /**
     * Construit la liste [label => chemin] des images disponibles dans la
     * médiathèque. Si le projet utilise déjà un visuel absent de la liste
     * (ancien chemin assets/img/...), il est ajouté pour rester sélectionnable.
     *
     * @return array<string, string>
     */
    private function buildImageChoices(?Project $project = null): array
    {
        $choices = [];

        foreach ($this->mediaRepository->findImages() as $media) {
            $choices[$media->getOriginalName()] = $media->getPath();
        }

        if (null !== $project) {
            $current = trim((string) $project->getImage());
            if ('' !== $current && !\in_array($current, $choices, true)) {
                $choices = [$current => $current] + $choices;
            }
        }

        return $choices;
    }
}
