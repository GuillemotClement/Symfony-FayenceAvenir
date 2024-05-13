<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('adresse')
            ->add('content')
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
            'data_class' => Event::class,
        ]);
    }
}
