<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/messages', name: 'admin_message_')]
#[IsGranted('ROLE_ADMIN')]
class MessageController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(MessageRepository $repository): Response
    {
        return $this->render('admin/messages.html.twig', [
            'messages' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    /**
     * Fragment utilisé dans la barre latérale : nombre de messages non lus.
     */
    public function unreadBadge(MessageRepository $repository): Response
    {
        return new Response((string) $repository->countUnread());
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Message $message, EntityManagerInterface $em): Response
    {
        if (!$message->isRead()) {
            $message->setIsRead(true);
            $em->flush();
        }

        return $this->render('admin/message_show.html.twig', ['message' => $message]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_message_' . $message->getId(), (string) $request->request->get('_token'))) {
            $em->remove($message);
            $em->flush();
            $this->addFlash('success', 'Message supprimé.');
        }

        return $this->redirectToRoute('admin_message_index');
    }
}
