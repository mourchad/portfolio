<?php

namespace App\Form;

use App\Entity\PathStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PathStepType extends AbstractType
{
    private const ICONS = [
        'Justice (institution)' => 'bi-bank',
        'Diplôme (études)' => 'bi-mortarboard-fill',
        'Entreprise / lieu' => 'bi-geo-alt-fill',
        'Antenne (télécoms)' => 'bi-broadcast',
        'Micro (langues)' => 'bi-megaphone',
        'École' => 'bi-school',
        'Ordinateur (code)' => 'bi-code-slash',
        'Cadenas (sécurité)' => 'bi-shield-lock',
        'Palette (design)' => 'bi-palette',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'étape',
                'help' => 'Ex : Stage académique — Cour Suprême du Bénin',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Nature',
                'choices' => PathStep::TYPES,
                'help' => 'Sert notamment à compter les « stages académiques » sur la page d\'accueil.',
            ])
            ->add('year', TextType::class, [
                'label' => 'Année affichée',
                'attr' => ['placeholder' => '2026'],
            ])
            ->add('dateLabel', TextType::class, [
                'label' => 'Dates détaillées',
                'attr' => ['placeholder' => '17 Avril – 17 Juin 2026'],
            ])
            ->add('place', TextType::class, [
                'label' => 'Lieu / organisme',
                'required' => false,
            ])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône du lieu',
                'required' => false,
                'placeholder' => '-- Choisir une icône --',
                'choices' => self::ICONS,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('tagsRaw', TextType::class, [
                'label' => 'Étiquettes',
                'help' => 'Séparez les étiquettes par des virgules (ex : Symfony, Twig, Doctrine)',
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Symfony, Twig, Doctrine'],
            ])
            ->add('featured', CheckboxType::class, [
                'label' => 'Mettre en avant cette étape (carte surlignée)',
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position d\'affichage',
                'help' => 'Les étapes sont affichées du plus petit au plus grand numéro.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PathStep::class,
        ]);
    }
}
