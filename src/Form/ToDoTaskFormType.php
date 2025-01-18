<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ToDoTaskFormType extends AbstractType {
    /**
     * Construit un formulaire pour ajouter une tâche dans la To Do List
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder
            // Ajoute un champs "zone de texte" pour ajouter une idée
            ->add('task', TextareaType::class, [
                'required' => true,
                'label' => 'Votre idée : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir ce champ'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Ecrivez ici votre idée',
                    'class' => 'form-group form-control',
                    'maxlength' => 1000,
                    'rows' => 5,
                    'cols' => 50,
                ],
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}