<?php

namespace App\Controller\Admin;

use App\Entity\SocialLink;
use App\Form\SocialLinkType;
use App\Repository\SocialLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reseaux', name: 'admin_social_')]
#[IsGranted('ROLE_ADMIN')]
class SocialLinkController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(SocialLinkRepository $repository): Response
    {
        return $this->render('admin/social_index.html.twig', [
            'links' => $repository->findAllOrdered(),
        ]);
    }

    #[Route('/nouveau', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $link = new SocialLink();
        $form = $this->createForm(SocialLinkType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($link);
            $em->flush();

            $this->addFlash('success', 'Lien « ' . $link->getLabel() . ' » ajouté.');

            return $this->redirectToRoute('admin_social_index');
        }

        return $this->render('admin/social_form.html.twig', [
            'form' => $form,
            'link' => $link,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, SocialLink $link, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SocialLinkType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Lien « ' . $link->getLabel() . ' » mis à jour.');

            return $this->redirectToRoute('admin_social_index');
        }

        return $this->render('admin/social_form.html.twig', [
            'form' => $form,
            'link' => $link,
            'isEdit' => true,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, SocialLink $link, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_social_' . $link->getId(), (string) $request->request->get('_token'))) {
            $em->remove($link);
            $em->flush();
            $this->addFlash('success', 'Lien supprimé.');
        }

        return $this->redirectToRoute('admin_social_index');
    }
}
