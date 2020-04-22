<?php

namespace App\Form;

use App\Entity\Setting;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('address')
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('smtpserver')
            ->add('smtpemail')
            ->add('smtppassword', PasswordType::class, [
                'attr' =>[
                    'require' => false,
                ]
            ])
            ->add('smtpport')
            ->add('facebook')
            ->add('instagram')
            ->add('twitter')
            ->add('aboutus', null, array(
                'attr' => [
                    'class' => 'ckeditor',
                ]
            ))

          ->add('contact' , null, array(
              'attr' => [
                  'class' => 'ckeditor',
              ]
          ))

            ->add('reference', CKEditorType::class)


            ->add('status' ,  ChoiceType::class,[
                'choices' => [
                    'True' => 'True',
                     'False' => 'False'],

                ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}
