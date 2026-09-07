<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\PathStep;
use App\Entity\Profile;
use App\Form\ContactType;
use App\Repository\PathStepRepository;
use App\Repository\ProfileRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    /**
     * Délai minimal attendu avant soumission du formulaire (piège anti-bot).
     */
    private const FORM_MIN_FILL_SECONDS = 3.0;

    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projectRepository,
        PathStepRepository $pathStepRepository,
        SkillRepository $skillRepository,
        ProfileRepository $profileRepository,
        MailerInterface $mailer,
        RateLimiterFactoryInterface $contactFormLimiter,
        LoggerInterface $logger,
    ): Response {
        // Réglages éditoriaux : l'email du profil pilote aussi la destination des messages.
        $profile = $profileRepository->findMain() ?? (new Profile())->setEmail(Profile::DEFAULT_EMAIL);
        $recipient = $profile->getEmail() ?? Profile::DEFAULT_EMAIL;

        $contact = new Message();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // 1) Limitation de débit : 5 soumissions maximum par IP et par heure.
            $limiter = $contactFormLimiter->create($request->getClientIp() ?? 'inconnu');
            if (!$limiter->consume()->isAccepted()) {
                $this->addFlash('danger', 'Trop de messages envoyés depuis cette connexion. Merci de réessayer dans une heure.');

                return $this->redirect($this->generateUrl('app_home') . '#contact');
            }

            // 2) Détection des robots : honeypot rempli ou formulaire soumis trop vite.
            $honeypot = trim((string) $form->get('company')->getData());
            $loadedAt = (float) $form->get('formLoadedAt')->getData();
            $fillTime = microtime(true) - $loadedAt;
            $looksLikeBot = '' !== $honeypot || $fillTime < self::FORM_MIN_FILL_SECONDS;

            if ($looksLikeBot) {
                // Réponse volontairement identique à un succès : le robot ne comprend pas qu'il a été filtré.
                $this->addFlash('success', 'Votre message a bien été envoyé.');

                return $this->redirect($this->generateUrl('app_home') . '#contact');
            }

            // 3) Soumission légitime : enregistrement + notification.
            if ($form->isValid()) {
                $em->persist($contact);
                $em->flush();

                try {
                    $email = (new Email())
                        // L'expéditeur doit correspondre au compte SMTP authentifié (Gmail).
                        ->from(new Address($recipient, 'Portfolio M - Creative & Digital'))
                        ->to($recipient)
                        ->replyTo($contact->getEmail())
                        ->subject('[Portfolio] ' . ($contact->getSubject() ?: 'Nouveau message de ' . $contact->getName()))
                        ->text(
                            "Nom : {$contact->getName()}\n"
                            . "Email : {$contact->getEmail()}\n\n"
                            . $contact->getContent()
                        );
                    $mailer->send($email);
                    $logger->info('[Portfolio] Email envoyé avec succès à ' . $recipient);
                } catch (TransportExceptionInterface $e) {
                    // Le message reste enregistré en base et visible dans l'espace admin,
                    // mais on trace l'échec d'envoi pour diagnostic.
                    $logger->error('[Portfolio] Échec envoi email : ' . $e->getMessage());
                }

                $this->addFlash('success', 'Votre message a bien été envoyé. Je vous répondrai dans les plus brefs délais !');

                return $this->redirect($this->generateUrl('app_home') . '#contact');
            }
        }

        return $this->render('home/index.html.twig', [
            'projects' => $projectRepository->findAllOrdered(),
            'pathSteps' => $pathStepRepository->findAllOrdered(),
            'stageCount' => $pathStepRepository->countByType(PathStep::TYPE_STAGE),
            'skills' => $skillRepository->findAllGrouped(),
            'techCount' => max(1, $skillRepository->countUniqueTags()),
            'profile' => $profile,
            'form' => $form,
        ]);
    }
}
