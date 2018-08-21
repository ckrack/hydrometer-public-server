<?php

namespace App\Form;

use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use \DateTime;

class FermentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'begin',
                DateType::class,
                [
                    'html5' => false,
                    'widget' => 'single_text',
                    'empty_data' => new DateTime('now')
                ]
            )
            ->add(
                'end',
                DateType::class,
                [
                    'html5' => false,
                    'widget' => 'single_text',
                    'empty_data' => new DateTime('3 weeks')
                ]
            )
            ->add('public')
            ->add(
                'hydrometer',
                EntityType::class,
                [
                    // looks for choices from this entity
                    'class' => Hydrometer::class,
                    // uses the Hydrometer.name property as the visible option string
                    'choice_label' => 'name'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fermentation::class,
        ]);
    }
}
