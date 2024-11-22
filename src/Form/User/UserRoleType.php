<?php

namespace App\Form\User;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRoleType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        
        $builder->add('roles', ChoiceType::class, [
            'placeholder' => 'Select one role',
            'choices' => [
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN'
            ],
            'multiple' => false,
            'required' => true,
            'row_attr' => [
                'class' => 'form-floating mb-3'
            ],
            'empty_data' => null
        ]);
        
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }
    
}