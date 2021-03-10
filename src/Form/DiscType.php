<?php

namespace App\Form;

use App\Entity\Disc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('artist')
            ->add('support')
             ->add('imageFile', VichImageType::class, [
                'label' => 'Image (Fichier JPG ou PNG)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => ' Supprimer ? ',
                'download_uri' => false,
                'imagine_pattern' => 'squared_thumbnail_small'
            ])
            ->add('comments')
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Disc::class,
        ]);
    }
}
