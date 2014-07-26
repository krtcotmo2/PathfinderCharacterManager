<?php

namespace Troulite\PathfinderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Troulite\PathfinderBundle\Entity\ClassDefinition;
use Troulite\PathfinderBundle\Entity\ClassPower;

/**
 * Class LoadClassDefinitionData
 *
 * @package Troulite\PathfinderBundle\DataFixtures\ORM
 */
class LoadClassDefinitionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $bab = array();
        $reflexes = array();
        $fortitude = array();
        $will = array();
        for ($i = 0; $i < 20; $i++) {
            $bab[] = $i + 1;
            $reflexes[] = ((int)(($i + 1) / 2)) + 2;
            $fortitude[] = ((int)(($i + 1) / 2)) + 2;
            $will[] = (int)(($i + 1) / 3);
        }
        $spellsPerDay = array(
            1 => array(-1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4),
            2 => array(-1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4),
            3 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3),
            4 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 3)
        );

        $ranger = new ClassDefinition();
        $ranger
            ->setName("Ranger")
            ->setHpDice(10)
            ->setSkillPoints(6)
            ->setBab($bab)
            ->setReflexes($reflexes)
            ->setFortitude($fortitude)
            ->setWill($will)
            ->setSpellsPerDay($spellsPerDay)
            ->addClassSkill($this->getReference('climb'))
            ->addClassSkill($this->getReference('craft'))
            ->addClassSkill($this->getReference('handleAnimal'))
            ->addClassSkill($this->getReference('heal'))
            ->addClassSkill($this->getReference('intimidate'))
            ->addClassSkill($this->getReference('knowledgeDungeoneering'))
            ->addClassSkill($this->getReference('knowledgeGeography'))
            ->addClassSkill($this->getReference('knowledgeNature'))
            ->addClassSkill($this->getReference('perception'))
            ->addClassSkill($this->getReference('profession'))
            ->addClassSkill($this->getReference('ride'))
            ->addClassSkill($this->getReference('spellcraft'))
            ->addClassSkill($this->getReference('stealth'))
            ->addClassSkill($this->getReference('survival'))
            ->addClassSkill($this->getReference('swim'));

        $power = (new ClassPower())
            ->setName('First Favored Enemy (+2)')
            ->setLevel(1)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'melee-attack-roll' => array('type' => null, 'value' => 2),
                    'melee-damage-roll'  => array('type' => null, 'value' => 2),
                    'ranged-attack-roll' => array('type' => null, 'value' => 2),
                    'ranged-damage-roll' => array('type' => null, 'value' => 2),
                    'bluff'      => array('type' => null, 'value' => 2),
                    'perception' => array('type' => null, 'value' => 2),
                    'sense-motive' => array('type' => null, 'value' => 2),
                    'survival' => array('type' => null, 'value' => 2),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-enemy-1', $power);

        $power = (new ClassPower())
            ->setName('Track')
            ->setLevel(1)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    // 1 == ranger class id
                    'survival' => array('type' => null, 'value' => 'max(1, div(c.getLevel(1), 2))'),
                )
            );
        $ranger->addPower($power);
        $this->setReference('track', $power);

        $power = (new ClassPower())
            ->setName('Wild Empathy')
            ->setLevel(1)
            ->setClass($ranger)
            ->setPassive(false);
        $ranger->addPower($power);
        $this->setReference('wild-empathy', $power);

        $power = (new ClassPower())
            ->setName('First Combat Style Feat')
            ->setLevel(2)
            ->setClass($ranger)
            ->setPassive(true)
            ->setEffects(
                array('extra_feats' => ['type' => 'class', 'value' => 1])
            );
        $ranger->addPower($power);
        $this->setReference('combat-style-feat-1', $power);

        $power = (new ClassPower())
            ->setName('Endurance')
            ->setLevel(3)
            ->setClass($ranger)
            ->setPassive(true);

        $ranger->addPower($power);
        $this->setReference('endurance', $power);

        $power = (new ClassPower())
            ->setName('Favored Terrain +2')
            ->setLevel(3)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'initiative' => array('type' => null, 'value' => 2),
                    'knowledge-geography' => array('type' => null, 'value' => 2),
                    'perception' => array('type' => null, 'value' => 2),
                    'stealth' => array('type' => null, 'value' => 2),
                    'survival' => array('type' => null, 'value' => 2),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-terrain-1', $power);

        $power = (new ClassPower())
            ->setName("Hunter's Bond")
            ->setLevel(4)
            ->setClass($ranger)
            ->setPassive(false);
        $ranger->addPower($power);
        $this->setReference('hunter-bond', $power);

        $power = (new ClassPower())
            ->setName('Second Favored Enemy (+4)')
            ->setLevel(5)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'melee-attack-roll'  => array('type' => null, 'value' => 4),
                    'melee-damage-roll'  => array('type' => null, 'value' => 4),
                    'ranged-attack-roll' => array('type' => null, 'value' => 4),
                    'ranged-damage-roll' => array('type' => null, 'value' => 4),
                    'bluff'              => array('type' => null, 'value' => 4),
                    'perception'         => array('type' => null, 'value' => 4),
                    'sense-motive'       => array('type' => null, 'value' => 4),
                    'survival'           => array('type' => null, 'value' => 4),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-enemy-2', $power);

        $power = (new ClassPower())
            ->setName('Second Combat Style Feat')
            ->setLevel(6)
            ->setClass($ranger)
            ->setPassive(true)
            ->setEffects(
                array('extra_feats' => ['type' => 'class', 'value' => 1])
            );
        $ranger->addPower($power);
        $this->setReference('combat-style-feat-2', $power);

        $power = (new ClassPower())
            ->setName('Woodland Stride')
            ->setLevel(7)
            ->setClass($ranger)
            ->setPassive(true);
        $ranger->addPower($power);
        $this->setReference('woodland-stride', $power);

        $power = (new ClassPower())
            ->setName('Swift Tracker')
            ->setLevel(8)
            ->setClass($ranger)
            ->setPassive(false);
        $ranger->addPower($power);
        $this->setReference('swift-tracker', $power);

        $power = (new ClassPower())
            ->setName('Favored Terrain +4')
            ->setLevel(8)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'initiative'          => array('type' => null, 'value' => 4),
                    'knowledge-geography' => array('type' => null, 'value' => 4),
                    'perception'          => array('type' => null, 'value' => 4),
                    'stealth'             => array('type' => null, 'value' => 4),
                    'survival'            => array('type' => null, 'value' => 4),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-terrain-2', $power);

        $power = (new ClassPower())
            ->setName('Evasion')
            ->setLevel(9)
            ->setClass($ranger)
            ->setPassive(true);
        $ranger->addPower($power);
        $this->setReference('evasion', $power);

        $power = (new ClassPower())
            ->setName('Third Combat Style Feat')
            ->setLevel(10)
            ->setClass($ranger)
            ->setPassive(true)
            ->setEffects(
                array('extra_feats' => ['type' => 'class', 'value' => 1])
            );
        $ranger->addPower($power);
        $this->setReference('combat-style-feat-3', $power);

        $power = (new ClassPower())
            ->setName('Third Favored Enemy (+6)')
            ->setLevel(10)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'melee-attack-roll'  => array('type' => null, 'value' => 6),
                    'melee-damage-roll'  => array('type' => null, 'value' => 6),
                    'ranged-attack-roll' => array('type' => null, 'value' => 6),
                    'ranged-damage-roll' => array('type' => null, 'value' => 6),
                    'bluff'              => array('type' => null, 'value' => 6),
                    'perception'         => array('type' => null, 'value' => 6),
                    'sense-motive'       => array('type' => null, 'value' => 6),
                    'survival'           => array('type' => null, 'value' => 6),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-enemy-3', $power);

        $power = (new ClassPower())
            ->setName('Quarry')
            ->setLevel(11)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'ranged-attack-roll' => array('type' => null, 'value' => 2),
                    'melee-attack-roll'  => array('type' => null, 'value' => 2),
                )
            );
        $ranger->addPower($power);
        $this->setReference('quarry', $power);

        $power = (new ClassPower())
            ->setName('Camouflage')
            ->setLevel(12)
            ->setClass($ranger)
            ->setPassive(true);
        $ranger->addPower($power);
        $this->setReference('camouflage', $power);

        $power = (new ClassPower())
            ->setName('Favored Terrain +6')
            ->setLevel(13)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'initiative'          => array('type' => null, 'value' => 6),
                    'knowledge-geography' => array('type' => null, 'value' => 6),
                    'perception'          => array('type' => null, 'value' => 6),
                    'stealth'             => array('type' => null, 'value' => 6),
                    'survival'            => array('type' => null, 'value' => 6),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-terrain-3', $power);

        $power = (new ClassPower())
            ->setName('Fourth Combat Style Feat')
            ->setLevel(14)
            ->setClass($ranger)
            ->setPassive(true)
            ->setEffects(
                array('extra_feats' => ['type' => 'class', 'value' => 1])
            );
        $ranger->addPower($power);
        $this->setReference('combat-style-feat-4', $power);

        $power = (new ClassPower())
            ->setName('Fourth Favored Enemy (+8)')
            ->setLevel(15)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'melee-attack-roll'  => array('type' => null, 'value' => 8),
                    'melee-damage-roll'  => array('type' => null, 'value' => 8),
                    'ranged-attack-roll' => array('type' => null, 'value' => 8),
                    'ranged-damage-roll' => array('type' => null, 'value' => 8),
                    'bluff'              => array('type' => null, 'value' => 8),
                    'perception'         => array('type' => null, 'value' => 8),
                    'sense-motive'       => array('type' => null, 'value' => 8),
                    'survival'           => array('type' => null, 'value' => 8),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-enemy-4', $power);

        $power = (new ClassPower())
            ->setName('Improved Evasion')
            ->setLevel(16)
            ->setClass($ranger)
            ->setPassive(true);
        $ranger->addPower($power);
        $this->setReference('improved-evasion', $power);

        $power = (new ClassPower())
            ->setName('Hide in Plain Sight')
            ->setLevel(17)
            ->setClass($ranger)
            ->setPassive(false);
        $ranger->addPower($power);
        $this->setReference('hide-in-plain-sight', $power);

        $power = (new ClassPower())
            ->setName('Fifth Combat Style Feat')
            ->setLevel(18)
            ->setClass($ranger)
            ->setPassive(true)
            ->setEffects(
                array('extra_feats' => ['type' => 'class', 'value' => 1])
            );
        $ranger->addPower($power);
        $this->setReference('combat-style-feat-5', $power);

        $power = (new ClassPower())
            ->setName('Favored Terrain +8')
            ->setLevel(18)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'initiative'          => array('type' => null, 'value' => 8),
                    'knowledge-geography' => array('type' => null, 'value' => 8),
                    'perception'          => array('type' => null, 'value' => 8),
                    'stealth'             => array('type' => null, 'value' => 8),
                    'survival'            => array('type' => null, 'value' => 8),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-terrain-4', $power);

        $power = (new ClassPower())
            ->setName('Improved Quarry')
            ->setLevel(19)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'ranged-attack-roll' => array('type' => null, 'value' => 4),
                    'melee-attack-roll'  => array('type' => null, 'value' => 4),
                )
            );
        $ranger->addPower($power);
        $this->setReference('improved-quarry', $power);

        $power = (new ClassPower())
            ->setName('Master Hunter')
            ->setLevel(20)
            ->setClass($ranger)
            ->setPassive(false);
        $ranger->addPower($power);
        $this->setReference('master-hunter', $power);

        /**
         * @todo Missing knowledge bonus
         */
        $power = (new ClassPower())
            ->setName('Fifth Favored Enemy (+10)')
            ->setLevel(20)
            ->setClass($ranger)
            ->setPassive(false)
            ->setEffects(
                array(
                    'melee-attack-roll'  => array('type' => null, 'value' => 10),
                    'melee-damage-roll'  => array('type' => null, 'value' => 10),
                    'ranged-attack-roll' => array('type' => null, 'value' => 10),
                    'ranged-damage-roll' => array('type' => null, 'value' => 10),
                    'bluff'              => array('type' => null, 'value' => 10),
                    'perception'         => array('type' => null, 'value' => 10),
                    'sense-motive'       => array('type' => null, 'value' => 10),
                    'survival'           => array('type' => null, 'value' => 10),
                )
            );
        $ranger->addPower($power);
        $this->setReference('favored-enemy-4', $power);

        $manager->persist($ranger);
        $manager->flush();

        $this->addReference('ranger', $ranger);

        $bab = array();
        $reflexes = array();
        $fortitude = array();
        $will = array();
        for ($i = 0; $i < 20; $i++) {
            $bab[] = $i + 1;
            $reflexes[] = (int)(($i + 1) / 3);
            $fortitude[] = ((int)(($i + 1) / 2)) + 2;
            $will[] = (int)(($i + 1) / 3);
        }

        $barbarian = new ClassDefinition();
        $barbarian
            ->setName("Barbarian")
            ->setHpDice(12)
            ->setSkillPoints(4)
            ->setBab($bab)
            ->setReflexes($reflexes)
            ->setFortitude($fortitude)
            ->setWill($will)
            ->addClassSkill($this->getReference('climb'))
            ->addClassSkill($this->getReference('craft'))
            ->addClassSkill($this->getReference('handleAnimal'))
            ->addClassSkill($this->getReference('acrobatics'))
            ->addClassSkill($this->getReference('intimidate'))
            ->addClassSkill($this->getReference('knowledgeNature'))
            ->addClassSkill($this->getReference('perception'))
            ->addClassSkill($this->getReference('ride'))
            ->addClassSkill($this->getReference('survival'))
            ->addClassSkill($this->getReference('swim'));

        $manager->persist($barbarian);
        $manager->flush();

        $this->addReference('barbarian', $barbarian);

        $bab       = array();
        $reflexes  = array();
        $fortitude = array();
        $will      = array();
        for ($i = 0; $i < 20; $i++) {
            $bab[]       = $i + 1;
            $reflexes[]  = ((int)(($i + 1) / 3)) ;
            $fortitude[] = ((int)(($i + 1) / 2)) + 2;
            $will[]      = (int)(($i + 1) / 2) + 2;
        }
        $spellsPerDay = array(
            1 => array(-1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4),
            2 => array(-1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4),
            3 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3),
            4 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 1, 1, 1, 1, 2, 2, 3)
        );

        $paladin = new ClassDefinition();
        $paladin
            ->setName("Paladin")
            ->setHpDice(10)
            ->setSkillPoints(2)
            ->setBab($bab)
            ->setReflexes($reflexes)
            ->setFortitude($fortitude)
            ->setWill($will)
            ->setSpellsPerDay($spellsPerDay)
            ->addClassSkill($this->getReference('craft'))
            ->addClassSkill($this->getReference('diplomacy'))
            ->addClassSkill($this->getReference('handleAnimal'))
            ->addClassSkill($this->getReference('heal'))
            ->addClassSkill($this->getReference('knowledgeNobility'))
            ->addClassSkill($this->getReference('knowledgeReligion'))
            ->addClassSkill($this->getReference('profession'))
            ->addClassSkill($this->getReference('ride'))
            ->addClassSkill($this->getReference('senseMotive'))
            ->addClassSkill($this->getReference('spellcraft'));
        $power = (new ClassPower())
            ->setName('Divine Grace')
            ->setDescription(
                'At 2nd level, a paladin gains a bonus equal to her Charisma bonus (if any) on all Saving Throws.'
            )
            ->setLevel(2)
            ->setClass($paladin)
            ->setPassive(true)
            ->setEffects(
                array(
                    'fortitude' => array('type' => null, 'value' => 'c.getAbilityModifier(c.getCharisma())'),
                    'reflexes'  => array('type' => null, 'value' => 'c.getAbilityModifier(c.getCharisma())'),
                    'will'      => array('type' => null, 'value' => 'c.getAbilityModifier(c.getCharisma())')
                )
            );
        $paladin->addPower($power);

        $manager->persist($power);
        $manager->persist($paladin);
        $manager->flush();

        $this->addReference('divine-grace', $power);
        $this->addReference('paladin', $paladin);

        $bab       = array(0,1,2,3,3,4,5,6,6,7,8,9,9,10,11,12,12,13,14,15);
        $reflexes  = array();
        $fortitude = array();
        $will      = array();
        for ($i = 0; $i < 20; $i++) {
            $reflexes[]  = ((int)(($i + 1) / 2)) + 2;
            $fortitude[] = ((int)(($i + 1) / 3));
            $will[]      = (int)(($i + 1) / 2) + 2;
        }
        $spellsPerDay = array(
            1 => array( 1,  2,  3,  3,  4,  4,  4,  4,  5,  5,  5,  5,  5,  5,  5, 5, 5, 5, 5, 5),
            2 => array(-1, -1, -1,  1,  2,  3,  3,  4,  4,  4,  4,  5,  5,  5,  5, 5, 5, 5, 5, 5),
            3 => array(-1, -1, -1, -1, -1, -1,  1,  2,  3,  3,  4,  4,  4,  4,  5, 5, 5, 5, 5, 5),
            4 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1,  1,  2,  3,  3,  4,  4, 4, 4, 5, 5, 5),
            5 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,  1,  2,  3, 3, 4, 4, 5, 5),
            6 => array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 1, 2, 3, 4, 5),
        );

        $bard = new ClassDefinition();
        $bard
            ->setName("Bard")
            ->setHpDice(8)
            ->setSkillPoints(6)
            ->setBab($bab)
            ->setReflexes($reflexes)
            ->setFortitude($fortitude)
            ->setWill($will)
            ->setSpellsPerDay($spellsPerDay)
            ->addClassSkill($this->getReference('acrobatics'))
            ->addClassSkill($this->getReference('appraise'))
            ->addClassSkill($this->getReference('bluff'))
            ->addClassSkill($this->getReference('climb'))
            ->addClassSkill($this->getReference('craft'))
            ->addClassSkill($this->getReference('diplomacy'))
            ->addClassSkill($this->getReference('disguise'))
            ->addClassSkill($this->getReference('escapeArtist'))
            ->addClassSkill($this->getReference('intimidate'))
            ->addClassSkill($this->getReference('knowledgeArcana'))
            ->addClassSkill($this->getReference('knowledgeDungeoneering'))
            ->addClassSkill($this->getReference('knowledgeEngineering'))
            ->addClassSkill($this->getReference('knowledgeGeography'))
            ->addClassSkill($this->getReference('knowledgeHistory'))
            ->addClassSkill($this->getReference('knowledgeLocal'))
            ->addClassSkill($this->getReference('knowledgeNature'))
            ->addClassSkill($this->getReference('knowledgeNobility'))
            ->addClassSkill($this->getReference('knowledgePlanes'))
            ->addClassSkill($this->getReference('knowledgeReligion'))
            ->addClassSkill($this->getReference('linguistics'))
            ->addClassSkill($this->getReference('perception'))
            ->addClassSkill($this->getReference('perform'))
            ->addClassSkill($this->getReference('profession'))
            ->addClassSkill($this->getReference('senseMotive'))
            ->addClassSkill($this->getReference('sleightOfHand'))
            ->addClassSkill($this->getReference('spellcraft'))
            ->addClassSkill($this->getReference('stealth'))
            ->addClassSkill($this->getReference('useMagicDevice'));

        $manager->persist($bard);
        $manager->flush();

        $this->addReference('bard',$bard);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }
}