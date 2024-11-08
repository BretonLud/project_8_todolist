<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])//->add('author') ===> must be the user authenticated
        ;
    }
}
