<?php

namespace Troulite\PathfinderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Troulite\PathfinderBundle\Entity\Abilities;

class LevelType extends AbstractType
{
    private $advancement;

    public function __construct($advancement)
    {
        $this->advancement = $advancement;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('classDefinition')
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $level = $event->getData();
                $form  = $event->getForm();

                // First level hpRoll should always be maxed out, so do not add the field in this case
                if ($level && $level->getCharacter()->getLevel() > 0) {
                    $form->add('hpRoll');
                }

                // Extra ability point
                if (
                    $level &&
                    $level->getCharacter()->getLevel() > 0 &&
                    $this->advancement[$level->getCharacter()->getLevel()]['ability']
                ) {
                    $form->add(
                        'extraAbility',
                        'choice',
                        array('choices' => array(
                            Abilities::STRENGTH => mb_convert_case(Abilities::STRENGTH, MB_CASE_TITLE),
                            Abilities::DEXTERITY => mb_convert_case(Abilities::DEXTERITY, MB_CASE_TITLE),
                            Abilities::CONSTITUTION => mb_convert_case(Abilities::CONSTITUTION, MB_CASE_TITLE),
                            Abilities::INTELLIGENCE => mb_convert_case(Abilities::INTELLIGENCE, MB_CASE_TITLE),
                            Abilities::WISDOM => mb_convert_case(Abilities::WISDOM, MB_CASE_TITLE),
                            Abilities::CHARISMA => mb_convert_case(Abilities::CHARISMA, MB_CASE_TITLE)
                        ))
                    );
                }

                // Extra feat
                if (
                    $level &&
                    $level->getCharacter()->getLevel() > 0 &&
                    $this->advancement[$level->getCharacter()->getLevel()]['feat']
                ) {
                    $form->add('feat');
                }
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
}
