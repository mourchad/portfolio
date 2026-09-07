<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use App\Form\SkillType;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/competences', name: 'admin_skill_')]
#[IsGranted('ROLE_ADMIN')]
class SkillController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(SkillRepository $repository): Response
    {
        return $this->render('admin/skill_index.html.twig', [
            'grouped' => $repository->findAllGrouped(),
        ]);
    }

    #[Route('/nouveau', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyRawFields($form, $skill);

            $em->persist($skill);
            $em->flush();

            $this->addFlash('success', 'Compétence « ' . $skill->getName() . ' » ajoutée.');

            return $this->redirectToRoute('admin_skill_index');
        }

        return $this->render('admin/skill_form.html.twig', [
            'form' => $form,
            'skill' => $skill,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Skill $skill, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SkillType::class, $skill);
        $form->get('tagsRaw')->setData(implode(', ', $skill->getTags()));
        $form->get('itemsRaw')->setData(implode("\n", $skill->getItems()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyRawFields($form, $skill);

            $em->flush();

            $this->addFlash('success', 'Compétence « ' . $skill->getName() . ' » mise à jour.');

            return $this->redirectToRoute('admin_skill_index');
        }

        return $this->render('admin/skill_form.html.twig', [
            'form' => $form,
            'skill' => $skill,
            'isEdit' => true,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Skill $skill, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_skill_' . $skill->getId(), (string) $request->request->get('_token'))) {
            $em->remove($skill);
            $em->flush();
            $this->addFlash('success', 'Compétence supprimée.');
        }

        return $this->redirectToRoute('admin_skill_index');
    }

    /**
     * Reporte les champs libres (étiquettes / points forts) dans l'entité.
     */
    private function applyRawFields(\Symfony\Component\Form\FormInterface $form, Skill $skill): void
    {
        $tagsRaw = (string) $form->get('tagsRaw')->getData();
        $skill->setTags(
            array_values(array_filter(array_map('trim', explode(',', $tagsRaw)), static fn ($t) => '' !== $t))
        );

        $itemsRaw = (string) $form->get('itemsRaw')->getData();
        $skill->setItems(
            array_values(array_filter(array_map('trim', explode("\n", $itemsRaw)), static fn ($t) => '' !== $t))
        );
    }
}
