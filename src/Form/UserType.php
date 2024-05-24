<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //on récupère l'user pour obtenir sa photo de profil
        //on récupère l'user depuis le builder
        $user = $builder->getData();
        $builder
            ->add('email', EmailType::class, ['label' => 'Email *'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe *'])
            ->add('firstname', null, ['label' => 'Prénom *'])
            ->add('lastname', null, ['label' => 'Nom *'])
            ->add('username', TextType::class, ['label' => 'Pseudo *'])
            ->add('pictureFile', FileType::class, [
                //si user a une photo, alors le champ n'est pas obligatoire
                'label'=> $user->getPicture() ? 'Image' : 'Image *',
                'mapped' => false,
                //si user n'as pas de picture alors le champ est required
                'required' => !$user->getPicture(),
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
