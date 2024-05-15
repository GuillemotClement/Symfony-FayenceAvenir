<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu'
            ])
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
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Culture/Animation' => 'culture',
                    'Societal' => 'societal',
                    'Environnement' => "environment",
                    'Sport' => 'sport',
                    'Energie' => 'energy',
                    'Agriculture' => 'agriculture',
                    'Tourisme' => 'tourism',
                ],
                'label' => 'Catégorie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
