<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PodcastFormType extends AbstractType {
    /**
     * Construit un formulaire pour le podcast
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder
            // Ajoute un champs "texte" pour ajouter un lien vers notre podcast
            ->add('lien', TextType::class, [
                'required' => true,
                'label' => 'Lien Youtube : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir ce champ'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Lien YouTube',
                    'class' => 'form-group form-control'
                ],
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}