<?php
/**
 * Created by PhpStorm.
 * User: jean-gui
 * Date: 12/07/14
 * Time: 16:16
 */
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

namespace Troulite\PathfinderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Troulite\PathfinderBundle\Model\AbilitiesBonuses;

/**
 * Class Abilities
 *
 * @package Troulite\PathfinderBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class Abilities
{
    /**
     * Strength identifier
     */
    const STRENGTH     = 'strength';
    /**
     * Dexterity identifier
     */
    const DEXTERITY    = 'dexterity';
    /**
     * Constitution identifier
     */
    const CONSTITUTION = 'constitution';
    /**
     * Intelligence identifier
     */
    const INTELLIGENCE = 'intelligence';
    /**
     * Wisdom identifier
     */
    const WISDOM       = 'wisdom';
    /**
     * Charisma identifier
     */
    const CHARISMA     = 'charisma';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseStrength;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseDexterity;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseConstitution;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseIntelligence;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseWisdom;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $baseCharisma;

    /**
     * @param $strength
     * @param $dexterity
     * @param $constitution
     * @param $intelligence
     * @param $wisdom
     * @param $charisma
     */
    public function __construct(
        $strength = 10,
        $dexterity = 10,
        $constitution = 10,
        $intelligence = 10,
        $wisdom = 10,
        $charisma = 10)
    {
        $this->baseStrength     = $strength;
        $this->baseDexterity    = $dexterity;
        $this->baseConstitution = $constitution;
        $this->baseIntelligence = $intelligence;
        $this->baseWisdom       = $wisdom;
        $this->baseCharisma     = $charisma;

        $this->bonuses = new AbilitiesBonuses();
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->bonuses = new AbilitiesBonuses();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get strength
     *
     * @return integer
     */
    public function getBaseStrength()
    {
        return $this->baseStrength;
    }

    /**
     * Set strength
     *
     * @param integer $baseStrength
     *
     * @return BaseCharacter
     */
    public function setBaseStrength($baseStrength)
    {
        $this->baseStrength = $baseStrength;

        return $this;
    }

    /**
     * Get dexterity
     *
     * @return integer
     */
    public function getBaseDexterity()
    {
        return $this->baseDexterity;
    }

    /**
     * Set dexterity
     *
     * @param integer $baseDexterity
     *
     * @return BaseCharacter
     */
    public function setBaseDexterity($baseDexterity)
    {
        $this->baseDexterity = $baseDexterity;

        return $this;
    }

    /**
     * Get constitution
     *
     * @return integer
     */
    public function getBaseConstitution()
    {
        return $this->baseConstitution;
    }

    /**
     * Set constitution
     *
     * @param integer $baseConstitution
     *
     * @return BaseCharacter
     */
    public function setBaseConstitution($baseConstitution)
    {
        $this->baseConstitution = $baseConstitution;

        return $this;
    }

    /**
     * Get intelligence
     *
     * @return integer
     */
    public function getBaseIntelligence()
    {
        return $this->baseIntelligence;
    }

    /**
     * Set intelligence
     *
     * @param integer $baseIntelligence
     *
     * @return BaseCharacter
     */
    public function setBaseIntelligence($baseIntelligence)
    {
        $this->baseIntelligence = $baseIntelligence;

        return $this;
    }

    /**
     * Get wisdom
     *
     * @return integer
     */
    public function getBaseWisdom()
    {
        return $this->baseWisdom;
    }

    /**
     * Set wisdom
     *
     * @param integer $baseWisdom
     *
     * @return BaseCharacter
     */
    public function setBaseWisdom($baseWisdom)
    {
        $this->baseWisdom = $baseWisdom;

        return $this;
    }

    /**
     * Get charisma
     *
     * @return integer
     */
    public function getBaseCharisma()
    {
        return $this->baseCharisma;
    }

    /**
     * Set charisma
     *
     * @param integer $baseCharisma
     *
     * @return BaseCharacter
     */
    public function setBaseCharisma($baseCharisma)
    {
        $this->baseCharisma = $baseCharisma;

        return $this;
    }
}
