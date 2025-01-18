<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    /**
     * Construit un formulaire pour l'inscription de l'utilisateur
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute le champs "texte" pour le nom
            ->add('name', TextType::class, array(
                'attr' => [
                    'placeholder' => 'Tapez votre nom',
                    'id' => 'name'
                ]
            ))

            // Ajoute le champs "texte" pour le prénom
            ->add('firstname', TextType::class, array(
                'attr' => [
                    'placeholder' => 'Tapez votre prénom',
                    'id' => 'firstname'
                ]
            ))

            // Ajoute le champs "texte" pour le mail
            ->add('email', EmailType::class, array(
                'attr' => [
                    'placeholder' => 'Tapez votre mail',
                    'id' => 'email'
                ]
            ))

            // Ajoute le champs "password" pour le mot de passe
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe *', 
                    'attr' => [
                        'placeholder' => 'Tapez votre mot de passe',
                        'id' => 'password'
                        ]
                    ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe *',
                    'attr' => [
                        'placeholder' => 'Confirmer votre mot de passe',
                        'id' => 'confirm-password'
                        ]
                    ],
            ])

            // Ajoute le champs "file" pour la photo de profil
            ->add('profilPhoto', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],

                'label' => 'Photo de profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '15360k',
                        'mimeTypes' => [
                            'image/gif', 
                            'image/png', 
                            'image/jpeg', 
                            'image/bmp', 
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir une image valide',
                    ])
                ],
            ])
            ->add('reciveMail', CheckboxType::class, [
                'label'    => '🎉 Recevoir des notifications 🎉',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label_attr'=> [
                    'class' => 'form-check-label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
