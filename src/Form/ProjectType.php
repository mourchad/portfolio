<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    private const CATEGORIES = [
        'Développement Web' => 'web',
        'Cybersécurité' => 'security',
        'Réseaux & Télécoms' => 'network',
        'Design Graphique' => 'design',
        'Vidéo' => 'video',
        'Autre' => 'other',
    ];

    private const ICONS = [
        'Globe (web)' => 'bi-globe2',
        'Ordinateur (code)' => 'bi-code-slash',
        'Cadenas (sécurité)' => 'bi-shield-lock',
        'Antenne (réseau)' => 'bi-broadcast',
        'Palette (design)' => 'bi-palette',
        'Caméra (vidéo)' => 'bi-camera-reels',
        'Livre (bibliothèque)' => 'bi-book-half',
        'Balance (justice)' => 'bi-bank',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du projet',
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Section (catégorie)',
                'help' => 'Le projet sera affiché dans cette section sur la page d\'accueil.',
                'choices' => self::CATEGORIES,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('technologiesRaw', TextType::class, [
                'label' => 'Technologies',
                'help' => 'Séparez les technologies par des virgules (ex : Python, Django, Bootstrap)',
                'mapped' => false,
                'attr' => ['placeholder' => 'Python, Django, SQLite'],
            ])
            ->add('image', ChoiceType::class, [
                'label' => 'Visuel (médiathèque)',
                'required' => false,
                'help' => 'Choisissez une image téléversée dans la médiathèque — ou laissez vide pour un visuel automatique avec icône.',
                'placeholder' => 'Aucun — visuel automatique avec icône',
                'choices' => $options['image_choices'],
            ])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône (si pas de visuel)',
                'required' => false,
                'placeholder' => '-- Choisir une icône --',
                'choices' => self::ICONS,
            ])
            ->add('url', UrlType::class, [
                'label' => 'Lien externe',
                'required' => false,
            ])
            ->add('featured', CheckboxType::class, [
                'label' => 'Projet vedette (mis en avant)',
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position d\'affichage',
                'help' => 'Les projets sont affichés du plus petit au plus grand numéro.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            // label => chemin relatif public. Alimenté par le contrôleur depuis la médiathèque.
            'image_choices' => [],
        ]);

        $resolver->setAllowedTypes('image_choices', ['array']);
    }
}
