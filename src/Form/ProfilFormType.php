<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ProfilFormType extends AbstractType
{
    /**
     * Construit un formulaire pour le profil de l'utilisateur
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute le champs "texte" pour le nom
            ->add('name')

            // Ajoute le champs "texte" pour le prénom
            ->add('firstname')

            // Ajoute le champs "password" pour le l'ancien mot de passe
            ->add('old_pwd', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Veuillez retaper votre ancien mot de passe',
                'attr' => [
                    'class' => 'mdp'
                ],
            ])

            // Ajoute le champs "password" pour le nouveau mot de passe
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'required' => false,
                'mapped' => false,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe'
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouveau mot de passe'
                ],
            ])

            // Ajoute le champs "file" pour la photo de profil
            ->add('profilPhoto', FileType::class, [
                'label' => 'Photo de profil',
                'attr' => [
                    'class' => 'form-control',
                ],

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
