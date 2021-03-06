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

namespace Troulite\PathfinderBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Troulite\PathfinderBundle\Entity\Character;
use Troulite\PathfinderBundle\Entity\CharacterFeat;
use Troulite\PathfinderBundle\Entity\ClassPower;
use Troulite\PathfinderBundle\Entity\Level;
use Troulite\PathfinderBundle\ExpressionLanguage\ExpressionLanguage;

/**
 * Class LevelUpFeatsType
 *
 * @package Troulite\PathfinderBundle\Form
 */
class LevelUpFeatsType extends AbstractType
{
    /**
     * @var
     */
    private $advancement;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param $advancement
     * @param EntityManager $em
     */
    public function __construct($advancement, EntityManager $em)
    {
        $this->advancement = $advancement;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->em;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($em) {
                /** @var $level Level */
                $level = $event->getData();
                $character = $level->getCharacter();
                $class = $level->getClassDefinition();
                $form  = $event->getForm();

                $featsToAdd = -$level->getFeats()->count();

                $choices = null;

                $availableFeats = $em->getRepository('TroulitePathfinderBundle:Feat')->findByAvailableFor($character);

                // Racial bonus feats
                if (
                    $character->getRace() &&
                    array_key_exists('extra_feats_per_level', $character->getRace()->getTraits())
                ) {
                    $effect = $character->getRace()->getTraits()['extra_feats_per_level'];
                    $value = (int)(new ExpressionLanguage())->evaluate(
                        $effect['value'],
                        array("c" => $character)
                    );
                    while ($value > 0) {
                        $choices[] = $availableFeats;
                        $featsToAdd++;
                        $value--;
                    }
                }

                $expressionLanguage = new ExpressionLanguage();

                // Class bonus feats
                if ($class) {
                    foreach ($level->getClassPowers() as $classPower) {
                        $power = $classPower->getClassPower();
                        $effects = $power->getEffects();
                        if ($power->hasEffects()) {
                            $ok = LevelUpFeatsType::checkPrerequisities($power, $character, $expressionLanguage);

                            if (!$ok) {
                                continue;
                            }

                            if (array_key_exists('extra_feats', $effects)) {
                                $effect = $effects['extra_feats'];
                                $value  = (int)(new ExpressionLanguage())->evaluate(
                                    $effect['value'],
                                    array("c" => $character)
                                );
                                for ($i = 0; $i < $value; $i++) {
                                    $choices[] = $availableFeats;
                                }
                                $featsToAdd += $value;
                            } elseif (array_key_exists('feat', $effects)) {
                                if ($effects['feat']['type'] === 'oneof') {
                                    $choices[] = $em->getRepository('TroulitePathfinderBundle:Feat')->findBy(
                                        array('name' => $effects['feat']['value'])
                                    );

                                    $featsToAdd += 1;
                                }
                            }
                        }
                    }
                }

                // Extra feat
                if (
                    $level &&
                    $character->getLevel() > 0 &&
                    $this->advancement[$character->getLevel()]['feat']
                ) {
                    $choices[] = $availableFeats;
                    $featsToAdd++;
                }

                while ($featsToAdd > 0) {
                    $level->addFeat(new CharacterFeat());
                    $featsToAdd--;
                }

                if ($level->getFeats()->count() > 0) {
                    $form->add(
                        'feats',
                        'collection',
                        array(
                            'type' => 'addcharacterfeat',
                            'options' => array(
                                'label' => /** @Ignore */ false,
                                'required' => false,
                                'level' => $level,
                                'choices' => $choices
                            )
                        )
                    );
                }
            }
        );
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Troulite\PathfinderBundle\Entity\Level'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'troulite_pathfinderbundle_level';
    }

    /**
     * @param $power
     * @param $character
     * @param $expressionLanguage
     *
     * @return bool
     */
    private static function checkPrerequisities(
        ClassPower $power,
        Character $character,
        ExpressionLanguage $expressionLanguage
    ) {
        $prerequisities = $power->getPrerequisities();

        foreach ($prerequisities as $key => $prereq) {
            if ($key === 'class-power') {
                foreach ($character->getClassPowers() as $classPower) {
                    $ok = $expressionLanguage->evaluate(
                        $prereq,
                        array("classPower" => $classPower)
                    );

                    if ($ok) {
                        return true;
                    }
                }

                return false;
            } else {
                $ok = $expressionLanguage->evaluate(
                    $prereq,
                    array("c" => $character)
                );

                if ($ok) {
                    return true;
                }
            }
        }

        return true;
    }
}
