<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/admin/medias', name: 'admin_media_')]
#[IsGranted('ROLE_ADMIN')]
class MediaController extends AbstractController
{
    private const MAX_SIZE_BYTES = 5242880; // 5 Mo

    /**
     * Types MIME attendus pour chaque extension autorisée.
     */
    private const MIME_BY_EXT = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'gif' => ['image/gif'],
        'avif' => ['image/avif'],
        'pdf' => ['application/pdf'],
        'txt' => ['text/plain'],
        'csv' => ['text/plain', 'text/csv', 'application/csv'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'odt' => ['application/vnd.oasis.opendocument.text'],
        'ods' => ['application/vnd.oasis.opendocument.spreadsheet'],
    ];

    #[Route('', name: 'index')]
    public function index(MediaRepository $repository): Response
    {
        $uploadForm = $this->createUploadForm();

        return $this->render('admin/media_index.html.twig', [
            'medias' => $repository->findAllRecent(),
            'uploadForm' => $uploadForm->createView(),
        ]);
    }

    #[Route('/ajouter', name: 'upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createUploadForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $files */
            $files = (array) $form->get('files')->getData();
            $uploaded = 0;
            $errors = [];

            foreach ($files as $file) {
                if (!$file instanceof UploadedFile || !$file->isValid()) {
                    $errors[] = 'Un fichier n\'a pas pu être récupéré.';

                    continue;
                }

                $error = $this->storeFile($file, $em);
                if (null === $error) {
                    ++$uploaded;
                } else {
                    $errors[] = sprintf('%s : %s', $file->getClientOriginalName(), $error);
                }
            }

            if ($uploaded > 0) {
                $this->addFlash('success', $uploaded . ' fichier' . ($uploaded > 1 ? 's' : '') . ' ajouté' . ($uploaded > 1 ? 's' : '') . ' à la médiathèque.');
            }
            foreach ($errors as $message) {
                $this->addFlash('danger', $message);
            }
        } elseif ($form->isSubmitted()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->redirectToRoute('admin_media_index');
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Media $media, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_media_' . $media->getId(), (string) $request->request->get('_token'))) {
            $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $media->getPath();

            if (is_file($filePath)) {
                if (unlink($filePath)) {
                    $this->addFlash('success', 'Fichier « ' . $media->getOriginalName() . ' » supprimé du disque.');
                } else {
                    $this->addFlash('warning', 'Fichier « ' . $media->getOriginalName() . ' » supprimé de la base mais impossible de supprimer le fichier du disque.');
                }
            }

            $em->remove($media);
            $em->flush();
        }

        return $this->redirectToRoute('admin_media_index');
    }

    private function createUploadForm(): \Symfony\Component\Form\FormInterface
    {
        return $this->createFormBuilder()
            ->add('files', FileType::class, [
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => implode(',', array_map(
                        static fn (string $exts) => '.' . $exts,
                        array_merge(...array_values(Media::ALLOWED))
                    )),
                ],
                'constraints' => [
                    new Assert\All([
                        'constraints' => [
                            new Assert\File(maxSize: '5M', maxSizeMessage: 'Le fichier {{ name }} dépasse la limite de 5 Mo.'),
                        ],
                    ]),
                ],
            ])
            ->getForm();
    }

    /**
     * Valide puis déplace le fichier. Retourne null en cas de succès,
     * sinon un message d'erreur destiné à l'administrateur.
     */
    private function storeFile(UploadedFile $file, EntityManagerInterface $em): ?string
    {
        $originalName = trim((string) $file->getClientOriginalName());
        $extension = strtolower($file->getClientOriginalExtension() ?: '');

        // 1) Extension dans la liste blanche ?
        $kind = null;
        foreach (Media::ALLOWED as $candidateKind => $extensions) {
            if (\in_array($extension, $extensions, true)) {
                $kind = $candidateKind;

                break;
            }
        }

        if (null === $kind) {
            return 'type de fichier non autorisé (' . ($extension ?: 'sans extension') . ').';
        }

        // 2) Type MIME réel cohérent avec l'extension ?
        $detectedMime = (string) $file->getMimeType();
        if (!\in_array($detectedMime, self::MIME_BY_EXT[$extension], true)) {
            return 'contenu incohérent avec l\'extension « ' . $extension . ' ».';
        }

        // 3) Taille raisonnable.
        $size = (int) $file->getSize();
        if ($size > self::MAX_SIZE_BYTES) {
            return 'fichier trop volumineux (5 Mo maximum).';
        }

        // 4) Nom aléatoire impossible à deviner, aucune donnée utilisateur dans le chemin.
        $filename = bin2hex(random_bytes(12)) . '.' . $extension;
        $directory = $this->getParameter('kernel.project_dir') . '/public/uploads/media';

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return 'impossible de créer le dossier de stockage.';
            }
        }

        try {
            $file->move($directory, $filename);
        } catch (\Exception) {
            return 'échec de l\'enregistrement sur le disque.';
        }

        $media = (new Media())
            ->setFilename($filename)
            ->setOriginalName('' !== $originalName ? $originalName : $filename)
            ->setMimeType($detectedMime)
            ->setSize($size)
            ->setKind($kind);
        $em->persist($media);
        $em->flush();

        return null;
    }
}
