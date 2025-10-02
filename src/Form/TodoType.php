<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class TodoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', TextType::class, [
                'label' => 'Tâche *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de votre tâche'
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez votre tâche (optionnel)'
                ],
                'required' => false
            ])
            ->add('dueDate', DateTimeType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('isCompleted', CheckboxType::class, [
                'label' => 'Tâche terminée',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'Priorité',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 5
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
        ]);
    }
}