<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Entity\Project;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard')]
    public function index(MessageRepository $messageRepository, ProjectRepository $projectRepository): Response
    {
        $lastMessage = $messageRepository->findOneBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/dashboard.html.twig', [
            'messagesCount' => $messageRepository->countAll(),
            'unreadCount' => $messageRepository->countUnread(),
            'projectsCount' => $projectRepository->countAll(),
            'lastMessageAt' => $lastMessage?->getCreatedAt(),
            'latestMessages' => $messageRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'latestProjects' => $projectRepository->findBy([], ['createdAt' => 'DESC'], 4),
        ]);
    }
}
