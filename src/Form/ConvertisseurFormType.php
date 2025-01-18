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

class ConvertisseurFormType extends AbstractType
{
    /**
     * Construit un formulaire pour le convertisseur
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            // Ajoute un champs "file" pour convertir le fichier
            ->add('file', FileType::class, [
                'attr' => [
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
            
            // ajoute un champs
            ->add('typeoffile', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-fields, form-select',
                ],
                'choices' => [
                    '--- Veuillez choisir un élément ---' => '',
                    'pdf' => 'pdf',
                    'Microsoft Office' => [
                        'docx' => 'docx',
                        'pptx' => 'pptx',
                        'xlsx' => 'xlsx',
                    ],
                    'Libre Office / OpenOffice' => [
                        'odt' => 'odt',
                        'odp' => 'odp',
                        'ods' => 'ods',
                    ],
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
