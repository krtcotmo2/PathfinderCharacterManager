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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MagicType
 *
 * @package Troulite\PathfinderBundle\Form\Classes
 */
class MagicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('preparationNeeded',
                'checkbox',
                array(
                    'required'                       => false,
                    'horizontal_label_class'         => 'col-sm-2',
                    'horizontal_input_wrapper_class' => 'col-sm-10'
                )
            )
            ->add('ableToLearnLowerLevelSpells',
                'checkbox',
                array(
                    'required'                       => false,
                    'horizontal_label_class'         => 'col-sm-2',
                    'horizontal_input_wrapper_class' => 'col-sm-10'
                )
            )
            ->add('ableToLearnNewSpells',
                'checkbox',
                array(
                    'required'                       => false,
                    'horizontal_label_class'         => 'col-sm-2',
                    'horizontal_input_wrapper_class' => 'col-sm-10'
                )
            )
            ->add('spellsPerDay',
                'collection',
                array(
                    'type'                           => 'collection',
                    'options'                        => array(
                        'widget_type'            => 'inline',
                        'type'                   => 'integer',
                        'label_format'           => 'Spell level: %name%',
                        'horizontal_label_class' => 'col-sm-2',
                        'options'                => array('label_render' => false)
                    ),
                    'horizontal_label_class'         => 'col-sm-2',
                    'horizontal_input_wrapper_class' => 'col-sm-10',
                    'required'                       => false
                ))
            ->add('knownSpellsPerLevel',
                'collection',
                array(
                    'type'                           => 'collection',
                    'options'                        => array(
                        'widget_type'            => 'inline',
                        'type'                   => 'integer',
                        'label_format'           => 'Spell level: %name%',
                        'horizontal_label_class' => 'col-sm-2',
                        'options'                => array('label_render' => false)
                    ),
                    'horizontal_label_class'         => 'col-sm-2',
                    'horizontal_input_wrapper_class' => 'col-sm-10',
                    'required'                       => false,
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Troulite\PathfinderBundle\Entity\ClassDefinition'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'troulite_pathfinderbundle_classdefinition';
    }
}
