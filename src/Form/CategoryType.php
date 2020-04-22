<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parentid', null, [
                'label' => "Parent Category",
            ])
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('image')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Published' => 'Published',
                    'Unpublished' => 'Unpublished',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
