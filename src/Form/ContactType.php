<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'attr' => ['placeholder' => 'Ex : Kossi Adjovi'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez indiquer votre nom.'),
                    new Length(max: 120),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['placeholder' => 'vous@exemple.com'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez indiquer votre email.'),
                    new Email(message: 'Adresse email invalide.'),
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => false,
                'attr' => ['placeholder' => 'Ex : Proposition de projet'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank(message: 'Veuillez écrire votre message.'),
                    new Length(
                        min: 10,
                        max: 5000,
                        minMessage: 'Votre message est trop court (10 caractères minimum).',
                        maxMessage: 'Votre message ne doit pas dépasser 5000 caractères.'
                    ),
                ],
                'attr' => ['rows' => 6],
            ])

            // --- Protections anti-bots (champs invisibles pour les humains) ---
            ->add('company', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'tabindex' => '-1',
                    'aria-hidden' => 'true',
                    'class' => 'hp-field',
                    'placeholder' => 'Ne pas remplir ce champ',
                ],
            ])
            ->add('formLoadedAt', HiddenType::class, [
                'mapped' => false,
                'data' => (string) microtime(true),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
