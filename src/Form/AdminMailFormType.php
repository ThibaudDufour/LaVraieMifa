<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminMailFormType extends AbstractType {
    /**
     * Construit un formulaire pour les mails de la page Admin
     * @param FormBuilderInterface $builder
     * @param array $option
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder
            // Ajoute un champs "texte" pour l'objet du mail
            ->add('objet', TextType::class, [
                'required' => true,
                'label' => 'Votre Objet : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir ce champ'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Objet',
                    'class' => 'form-group form-control'
                ],
                'mapped' => false
            ])
            
            // Ajoute un champs "zone de texte" pour le corps du mail
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Votre message : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir ce champ'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Ecrivez ici votre message',
                    'class' => 'form-group form-control',
                    'rows' => 10,
                    'cols' => 62
                ],
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}