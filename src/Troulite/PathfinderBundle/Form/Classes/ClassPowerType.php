<?php

/*
 * Copyright 2015 Jean-Guilhem Rouel
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Troulite\PathfinderBundle\Form\Classes;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Troulite\PathfinderBundle\Entity\Abilities;
use Troulite\PathfinderBundle\Form\DataTransformer\ArrayToJsonTransformer;

/**
 * Class ClassPowerType
 *
 * @package Troulite\PathfinderBundle\Form\Classes
 */
class ClassPowerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayToJsonTransformer = new ArrayToJsonTransformer();

        $builder
            ->add('name')
            ->add('shortDescription')
            ->add('longDescription')
            ->add('level')
            ->add('castable', null, array('required' => false))
            ->add('passive', null, array('required' => false))
            ->add(
                $builder->create('prerequisities', 'textarea', array('required' => false))
                    ->addModelTransformer($arrayToJsonTransformer)
            )
            ->add($builder->create('conditions', 'textarea', array('required' => false))
                ->addModelTransformer($arrayToJsonTransformer)
            )
            ->add($builder->create('externalConditions', 'textarea', array('required' => false))
                ->addModelTransformer($arrayToJsonTransformer)
            )
            ->add($builder->create('effects', 'textarea', array('required' => false))
                ->addModelTransformer($arrayToJsonTransformer)
            )
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Troulite\PathfinderBundle\Entity\ClassPower'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'power';
    }
}