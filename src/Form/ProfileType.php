<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aboutBadge', TextType::class, [
                'label' => 'Badge du hero (diplôme mis en avant)',
            ])
            ->add('typedRolesRaw', TextareaType::class, [
                'label' => 'Métiers de l\'animation de frappe',
                'help' => 'Un métier par ligne.',
                'mapped' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('heroLead', TextareaType::class, [
                'label' => 'Bio du hero',
                'help' => 'Courte présentation sous votre nom. Balises <em> autorisées pour les mots accentués.',
                'attr' => ['rows' => 4],
            ])
            ->add('photoPath', ChoiceType::class, [
                'label' => 'Photo de profil (médiathèque)',
                'required' => false,
                'placeholder' => 'Aucune — image par défaut',
                'choices' => $options['photo_choices'],
            ])
            ->add('cvPath', ChoiceType::class, [
                'label' => 'CV téléchargeable (médiathèque)',
                'required' => false,
                'placeholder' => 'Aucun — CV par défaut',
                'choices' => $options['cv_choices'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email public et destinataire des messages',
                'help' => 'Les messages du formulaire arrivent sur cette adresse.',
            ])
            ->add('phone1', TextType::class, [
                'label' => 'Téléphone principal',
                'required' => false,
            ])
            ->add('phone2', TextType::class, [
                'label' => 'Téléphone secondaire',
                'required' => false,
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse postale',
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('statMention', TextType::class, [
                'label' => 'Mention (barre de statistiques)',
                'help' => 'Ex : 16/20',
                'required' => false,
            ])
            ->add('footerBio', TextareaType::class, [
                'label' => 'Bio du pied de page',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Description pour Google (balise meta)',
                'required' => false,
                'attr' => ['rows' => 2],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            // label => chemin relatif public. Alimentés par le contrôleur depuis la médiathèque.
            'photo_choices' => [],
            'cv_choices' => [],
        ]);

        $resolver->setAllowedTypes('photo_choices', ['array']);
        $resolver->setAllowedTypes('cv_choices', ['array']);
    }
}
