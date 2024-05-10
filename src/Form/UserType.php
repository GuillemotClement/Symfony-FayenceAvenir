<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email *'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe *'])
            ->add('firstname', null, ['label' => 'Prénom *'])
            ->add('lastname', null, ['label' => 'Nom *'])
            ->add('pictureFile', FileType::class, [
                'label'=>'Image',
                'mapped' => false,
                'constraints' => [
                    new Image(
                        [
                            'mimeTypesMessage'=> 'Veuillez soumettre une image',
                            'maxSize' => '2M',
                            'maxSizeMessage' => 'Image trop lourde. La limite est de {{ limite}} {{ suffix }}'
                        ]
                    )
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
