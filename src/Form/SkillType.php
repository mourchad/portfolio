<?php

namespace App\Form;

use App\Entity\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillType extends AbstractType
{
    private const ICONS = [
        'Ordinateur (code)' => 'bi-code-slash',
        'Cadenas (sécurité)' => 'bi-shield-lock',
        'Antenne (réseau)' => 'bi-broadcast-pin',
        'Palette (design)' => 'bi-palette',
        'Caméra (vidéo)' => 'bi-camera-reels',
        'Ampoule (idée)' => 'bi-lightbulb',
        'Globe (web)' => 'bi-globe2',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'help' => 'Ex : Cybersécurité, Word, Anglais…',
            ])
            ->add('skillGroup', ChoiceType::class, [
                'label' => 'Emplacement sur le site',
                'choices' => Skill::GROUPS,
            ])
            ->add('percent', IntegerType::class, [
                'label' => 'Niveau (%)',
                'help' => 'Barre de progression des compétences/outils ; 90+ = langue maternelle, 60-89 = bon niveau, moins = intermédiaire.',
                'attr' => ['min' => 0, 'max' => 100],
            ])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône (blocs et expertises)',
                'required' => false,
                'placeholder' => '-- Choisir une icône --',
                'choices' => self::ICONS,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description courte',
                'required' => false,
                'help' => 'Texte sous le titre d\'un bloc de compétence — ou texte de la pastille pour une langue.',
                'attr' => ['rows' => 3],
            ])
            ->add('tagsRaw', TextareaType::class, [
                'label' => 'Étiquettes',
                'help' => 'Séparez par des virgules (ex : Kali Linux, Pentesting, OWASP)',
                'mapped' => false,
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('itemsRaw', TextareaType::class, [
                'label' => 'Points forts',
                'help' => 'Un point par ligne — uniquement utilisé par les cartes « Expertise ».',
                'mapped' => false,
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => "Création de sites web responsives\nOptimisation des performances"],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position d\'affichage',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }
}
