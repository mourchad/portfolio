<?php

namespace App\Form;

use App\Entity\SocialLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialLinkType extends AbstractType
{
    private const ICONS = [
        'LinkedIn' => 'bi-linkedin',
        'GitHub' => 'bi-github',
        'X / Twitter' => 'bi-twitter-x',
        'Facebook' => 'bi-facebook',
        'WhatsApp' => 'bi-whatsapp',
        'Instagram' => 'bi-instagram',
        'TikTok' => 'bi-tiktok',
        'YouTube' => 'bi-youtube',
        'Telegram' => 'bi-telegram',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'Nom du réseau',
                'help' => 'Utilisé pour l\'accessibilité (ex : LinkedIn).',
            ])
            ->add('url', UrlType::class, [
                'label' => 'Lien complet',
                'help' => 'Ex : https://www.linkedin.com/in/votre-profil ou https://wa.me/229XXXXXXXXX',
            ])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône',
                'choices' => self::ICONS,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position d\'affichage',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
