<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsFeedFormType extends AbstractType
{
    /**
     * Construit un formulaire pour la News Feed
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champs "zone de texte" pour le message 
            ->add('contentMessage', TextareaType::class, [
                'required' => false,
                'label' => 'Votre message : ',
                'attr' => [
                    'placeholder' => 'Ecrivez ici votre message',
                    'class' => 'form-group form-control',
                    'rows' => 10,
                    'cols' => 62,
                    'maxlength' => 255
                ],
                'mapped' => false
            ])

            // Ajoute un champs "text" pour coller le lien d'un Txeet, ...
            ->add('lien', TextType::class, [
                'attr' => [
                    'placeholder' => 'Copiez le lien ici',
                    'class' => 'form-fields, form-control',
                    'id' => 'customFile'
                ],

                'label' => ' ',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,
            ])
            
            // ajoute un champs "sleclt" pour choisir d'où porvient le lien 
            ->add('typeSocialMedia', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-fields, form-select',
                ],
                'choices' => [
                    '--- Veuillez choisir un élément ---' => '',
                    'Twitter' => 'twitter',
                    'YouTube' => 'youtube',
                    'TikTok' => 'tiktok',
                    'Spotify' => 'spotify'
                ],


                'label' => ' ',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
