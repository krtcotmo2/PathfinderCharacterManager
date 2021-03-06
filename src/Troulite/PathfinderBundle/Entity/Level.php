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

namespace Troulite\PathfinderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Level
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Level
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Character
     *
     * @ORM\ManyToOne(targetEntity="Character", inversedBy="levels")
     * @ORM\JoinColumn(name="character", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $character;

    /**
     * @var ClassDefinition
     *
     * @ORM\ManyToOne(targetEntity="ClassDefinition")
     * @ORM\JoinColumn(name="class", referencedColumnName="id")
     * @Assert\NotBlank()
     * @ORM\Cache()
     */
    private $classDefinition;

    /**
     * @var ClassDefinition parent class for prestige classes
     *
     * @ORM\ManyToOne(targetEntity="ClassDefinition")
     * @ORM\JoinColumn(name="parent_class", referencedColumnName="id")
     * @ORM\Cache()
     */
    private $parentClass;

    /**
     * @var Collection|SubClass[]
     *
     * @ORM\ManyToMany(targetEntity="SubClass", orphanRemoval=true)
     * @ORM\JoinTable(name="levels_subclasses",
     *      joinColumns={@ORM\JoinColumn(name="level_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="subclass_id", referencedColumnName="id")}
     *      )
     * @ORM\Cache()
     */
    private $subClasses;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $hpRoll;

    /**
     * Determines whether to add an extra skill point or HP when a favored class is chosen when leveling up
     *
     * @var string one of 'skill', 'hp'
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $extraPoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $extraAbility;

    /**
     * @var Collection|CharacterFeat[]
     *
     * @ORM\OneToMany(targetEntity="CharacterFeat", mappedBy="level", cascade={"all"}, orphanRemoval=true)
     */
    private $feats;

    /**
     * @var Collection|LevelSkill[]
     *
     * @ORM\OneToMany(targetEntity="LevelSkill", mappedBy="level", cascade={"all"}, orphanRemoval=true)
     */
    private $skills;

    /**
     * @var Collection|CharacterClassPower[]
     *
     * @ORM\OneToMany(targetEntity="CharacterClassPower", mappedBy="level", cascade={"all"}, orphanRemoval=true)
     */
    private $classPowers;

    /**
     * @var Collection|ClassSpell[]
     *
     * @ORM\ManyToMany(targetEntity="ClassSpell", orphanRemoval=true)
     * @ORM\JoinTable(name="levels_spells",
     *      joinColumns={@ORM\JoinColumn(name="level_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="classspell_id", referencedColumnName="id")}
     *      )
     */
    private $learnedSpells;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subClasses  = new ArrayCollection();
        $this->skills      = new ArrayCollection();
        $this->feats       = new ArrayCollection();
        $this->classPowers = new ArrayCollection();
        $this->learnedSpells = new ArrayCollection();
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
     * Set character
     *
     * @param Character $character
     *
     * @return $this
     */
    public function setCharacter(Character $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return Character
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Set classDefinition
     *
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    public function setClassDefinition(ClassDefinition $classDefinition)
    {
        $this->classDefinition = $classDefinition;

        return $this;
    }

    /**
     * Get classDefinition
     *
     * @return ClassDefinition
     */
    public function getClassDefinition()
    {
        return $this->classDefinition;
    }

    /**
     * @return ClassDefinition
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @param ClassDefinition $parentClass
     *
     * @return $this
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;

        return $this;
    }

    /**
     * @return Collection|SubClass[]
     */
    public function getSubClasses()
    {
        return $this->subClasses;
    }

    /**
     * @param SubClass $subClass
     *
     * @return $this
     */
    public function addSubClass($subClass)
    {
        $this->subClasses[] = $subClass;

        return $this;
    }

    /**
     * @param SubClass $subClass
     *
     * @return $this
     */
    public function removeSubClass($subClass)
    {
        $this->subClasses->removeElement($subClass);

        return $this;
    }

    /**
     * Set hpRoll
     *
     * @param integer $hpRoll
     *
     * @return $this
     */
    public function setHpRoll($hpRoll)
    {
        $this->hpRoll = $hpRoll;

        return $this;
    }

    /**
     * Get hpRoll
     *
     * @return integer
     */
    public function getHpRoll()
    {
        return $this->hpRoll;
    }

    /**
     * Set extraPoint
     *
     * @param string $extraPoint
     *
     * @return $this
     */
    public function setExtraPoint($extraPoint)
    {
        $this->extraPoint = $extraPoint;

        return $this;
    }

    /**
     * Get extraPoint
     *
     * @return string
     */
    public function getExtraPoint()
    {
        return $this->extraPoint;
    }

    /**
     * Add a skill or its value if this level already has that skill
     *
     * @param LevelSkill $skill
     *
     * @return $this
     */
    public function addSkill(LevelSkill $skill)
    {
        $matchingSkill = null;
        foreach ($this->getSkills() as $ls) {
            if ($ls->getSkill() === $skill->getSkill()) {
                $matchingSkill = $ls;
                break;
            }
        }

        if ($matchingSkill) {
            $matchingSkill->setValue($matchingSkill->getValue() + $skill->getValue());
        } else {
            $skill->setLevel($this);
            $this->skills[] = $skill;
        }

        return $this;
    }

    /**
     * Remove skills
     *
     * @param LevelSkill $skills
     */
    public function removeSkill(LevelSkill $skills)
    {
        $this->skills->removeElement($skills);
    }

    /**
     * Get skills
     *
     * @return Collection|LevelSkill[]
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Set extraAbility
     *
     * @param string $extraAbility
     * @return $this
     */
    public function setExtraAbility($extraAbility)
    {
        $this->extraAbility = $extraAbility;

        return $this;
    }

    /**
     * Get extraAbility
     *
     * @return string 
     */
    public function getExtraAbility()
    {
        return $this->extraAbility;
    }

    /**
     * Add feats
     *
     * @param CharacterFeat $feat
     *
     * @return $this
     */
    public function addFeat(CharacterFeat $feat = null)
    {
        $feat->setLevel($this);
        $this->feats[] = $feat;

        return $this;
    }

    /**
     * Remove feat
     *
     * @param CharacterFeat $feat
     */
    public function removeFeat(CharacterFeat $feat = null)
    {
        $this->feats->removeElement($feat);
    }

    /**
     * Get feats
     *
     * @return Collection|CharacterFeat[]
     */
    public function getFeats()
    {
        return $this->feats;
    }

    /**
     * Whether this level is in the favored class
     *
     * @return bool
     */
    public function isFavoredClass()
    {
        return $this->getClassDefinition() === $this->getCharacter()->getFavoredClass();
    }

    /**
     * Add classPowers
     *
     * @param CharacterClassPower $classPower
     *
     * @return $this
     */
    public function addClassPower(CharacterClassPower $classPower)
    {
        $classPower->setLevel($this);
        $this->classPowers[] = $classPower;

        return $this;
    }

    /**
     * Remove classPowers
     *
     * @param CharacterClassPower $classPower
     */
    public function removeClassPower(CharacterClassPower $classPower)
    {
        $this->classPowers->removeElement($classPower);
    }

    /**
     * Get classPowers
     *
     * @return Collection|CharacterClassPower[]
     */
    public function getClassPowers()
    {
        return $this->classPowers;
    }

    /**
     * Add learnedSpells
     *
     * @param ClassSpell $learnedSpell
     * 
     * @return $this
     */
    public function addLearnedSpell(ClassSpell $learnedSpell)
    {
        $this->learnedSpells[] = $learnedSpell;

        return $this;
    }

    /**
     * Remove learnedSpell
     *
     * @param ClassSpell $learnedSpell
     */
    public function removeLearnedSpell(ClassSpell $learnedSpell)
    {
        $this->learnedSpells->removeElement($learnedSpell);
    }

    /**
     * Get learnedSpells
     *
     * @return Collection|ClassSpell[]
     */
    public function getLearnedSpells()
    {
        return $this->learnedSpells;
    }

    /**
     * @param Collection|ClassSpell[] $learnedSpells
     *
     * @return $this
     */
    public function setLearnedSpells(Collection $learnedSpells)
    {
        $this->learnedSpells = $learnedSpells;

        return $this;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }


}
