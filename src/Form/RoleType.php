<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
              'label' => 'Rôle',
              'choices' => [
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER',
                'Dev' => 'ROLE_DEV',
                'Rédacteur' => 'ROLE_REDAC'
              ],
              'multiple' => true,
              'expanded' => true,
              'mapped' => true
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


