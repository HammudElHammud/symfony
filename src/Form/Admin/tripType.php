<?php

namespace App\Form\Admin;

use App\Entity\Admin\trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class tripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category')
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('content', TextareaType::class, [

                'attr' => [
                    'class' => 'ckeditor'
                ]
            ])

            ->add('trip_start', DateType::class)
            ->add('trip_end', DateType::class)
            ->add('image', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                ]
            )
            ->add('price')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    "Published" => "Published",
                    "Unpublished" => "Unpublished",

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => trip::class,
            'csrf_protection'=>false,
        ]);
    }
}
