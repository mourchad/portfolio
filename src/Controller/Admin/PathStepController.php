<?php

namespace App\Controller\Admin;

use App\Entity\PathStep;
use App\Form\PathStepType;
use App\Repository\PathStepRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/parcours', name: 'admin_path_')]
#[IsGranted('ROLE_ADMIN')]
class PathStepController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(PathStepRepository $repository): Response
    {
        return $this->render('admin/path_index.html.twig', [
            'steps' => $repository->findAllOrdered(),
        ]);
    }

    #[Route('/nouveau', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $step = new PathStep();

        $form = $this->createForm(PathStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw = (string) $form->get('tagsRaw')->getData();
            $step->setTags($this->splitTags($raw));

            $em->persist($step);
            $em->flush();

            $this->addFlash('success', 'Étape « ' . $step->getTitle() . ' » ajoutée au parcours.');

            return $this->redirectToRoute('admin_path_index');
        }

        return $this->render('admin/path_form.html.twig', [
            'form' => $form,
            'step' => $step,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, PathStep $step, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PathStepType::class, $step);
        $form->get('tagsRaw')->setData(implode(', ', $step->getTags()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw = (string) $form->get('tagsRaw')->getData();
            $step->setTags($this->splitTags($raw));

            $em->flush();

            $this->addFlash('success', 'Étape « ' . $step->getTitle() . ' » mise à jour.');

            return $this->redirectToRoute('admin_path_index');
        }

        return $this->render('admin/path_form.html.twig', [
            'form' => $form,
            'step' => $step,
            'isEdit' => true,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, PathStep $step, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_path_' . $step->getId(), (string) $request->request->get('_token'))) {
            $em->remove($step);
            $em->flush();
            $this->addFlash('success', 'Étape supprimée du parcours.');
        }

        return $this->redirectToRoute('admin_path_index');
    }

    /**
     * Découpe la saisie brute en liste d'étiquettes propres.
     *
     * @return string[]
     */
    private function splitTags(string $raw): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $raw)), static fn ($t) => '' !== $t));
    }
}
