<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Form;

use App\Entity\Hydrometer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HydrometerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'Name of the device, colour,..']])
            ->add('metricTemperature', ChoiceType::class, ['choices' => ['Celsius' => '°C', 'Fahrenheit' => '°F']])
            ->add('metricGravity', ChoiceType::class, ['choices' => ['Plato' => '°P', 'Specific gravity (SG)' => 'SG', 'Brix' => '%']])
            ->add('interval', IntegerType::class, ['attr' => ['min' => 60, 'max' => 22000, 'placeholder' => 'Interval in seconds, overrides internal config (iSpindel only)']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hydrometer::class,
        ]);
    }
}
