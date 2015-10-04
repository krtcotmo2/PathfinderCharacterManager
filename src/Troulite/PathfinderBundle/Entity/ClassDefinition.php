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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ClassDefinition
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\Cache()
 */
class ClassDefinition
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"class-definition"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"class-definition"})
     */
    private $name;

    /**
     * @var Collection|SubClass[]
     *
     * @ORM\OneToMany(targetEntity="SubClass", mappedBy="parent", cascade={"all"})
     * @ORM\Cache()
     * @Groups({"class-definition"})
     */
    private $subClasses;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"class-definition"})
     */
    private $subClassesNumber;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Groups({"class-definition"})
     */
    private $hpDice;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Groups({"class-definition"})
     */
    private $skillPoints;

    /**
     * @var integer[]
     *
     * @ORM\Column(type="json_array")
     * @Groups({"class-definition"})
     */
    private $bab;

    /**
     * @var integer[]
     *
     * @ORM\Column(type="json_array")
     * @Groups({"class-definition"})
     */
    private $reflexes;

    /**
     * @var integer[]
     *
     * @ORM\Column(type="json_array")
     * @Groups({"class-definition"})
     */
    private $fortitude;

    /**
     * @var integer[]
     *
     * @ORM\Column(type="json_array")
     * @Groups({"class-definition"})
     */
    private $will;

    /**
     * @var integer[]
     *
     * @ORM\Column(type="json_array", nullable=true)
     * @Groups({"class-definition"})
     */
    private $spellsPerDay;

    /**
     * @var Collection|Skill[]
     *
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="classes")
     * @ORM\JoinTable(name="class_skills",
     *      joinColumns={@ORM\JoinColumn(name="class_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id")}
     *      )
     * @ORM\Cache()
     * @Groups({"class-definition"})
     */
    private $classSkills;

    /**
     * @var Collection|ClassPower[]
     *
     * @ORM\OneToMany(targetEntity="ClassPower", mappedBy="class", cascade={"all"})
     * @ORM\Cache()
     * @Groups({"class-definition"})
     */
    private $powers;

    /**
     * @var string One of intelligence, wisdom or charisma
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Choice(choices={"intelligence", "wisdom", "charisma"})
     * @Groups({"class-definition"})
     */
    private $castingAbility;

    /**
     * @var bool true if this class' spells need to be prepared before being cast
     *
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"class-definition"})
     */
    private $preparationNeeded = true;

    /**
     * @var array Number of spells a character of this class knows per level
     *
     * @ORM\Column(type="json_array", nullable=true)
     * @Groups({"class-definition"})
     */
    private $knownSpellsPerLevel;

    /**
     * @var Collection|ClassSpell[]
     *
     * @ORM\OneToMany(targetEntity="ClassSpell", mappedBy="class", orphanRemoval=true, cascade={"all"})
     * @ORM\Cache()
     * @Groups({"class-definition"})
     */
    private $spells;

    /**
     * @var bool true if this class can learn lower level spells in higher level slots
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = false})
     * @Groups({"class-definition"})
     */
    private $ableToLearnLowerLevelSpells = false;

    /**
     * @var bool true if this class can learn new spells from spellbooks
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = false})
     * @Groups({"class-definition"})
     */
    private $ableToLearnNewSpells = false;

    /**
     * @var bool true if this is a prestige class
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = false})
     * @Groups({"class-definition"})
     */
    private $prestige = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subClasses  = new ArrayCollection();
        $this->powers      = new ArrayCollection();
        $this->spells      = new ArrayCollection();
        $this->classSkills = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ClassDefinition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set hpDice
     *
     * @param integer $hpDice
     * @return ClassDefinition
     */
    public function setHpDice($hpDice)
    {
        $this->hpDice = $hpDice;

        return $this;
    }

    /**
     * Get hpDice
     *
     * @return integer
     */
    public function getHpDice()
    {
        return $this->hpDice;
    }

    /**
     * Set skillPoints
     *
     * @param integer $skillPoints
     * @return ClassDefinition
     */
    public function setSkillPoints($skillPoints)
    {
        $this->skillPoints = $skillPoints;

        return $this;
    }

    /**
     * Get skillPoints
     *
     * @return integer
     */
    public function getSkillPoints()
    {
        return $this->skillPoints;
    }

    /**
     * Set bab
     *
     * @param array $bab
     * @return ClassDefinition
     */
    public function setBab($bab)
    {
        $this->bab = $bab;

        return $this;
    }

    /**
     * Get bab
     *
     * @return array
     */
    public function getBab()
    {
        return $this->bab;
    }

    /**
     * Set reflexes
     *
     * @param array $reflexes
     * @return ClassDefinition
     */
    public function setReflexes($reflexes)
    {
        $this->reflexes = $reflexes;

        return $this;
    }

    /**
     * Get reflexes
     *
     * @return array
     */
    public function getReflexes()
    {
        return $this->reflexes;
    }

    /**
     * Set fortitude
     *
     * @param array $fortitude
     * @return ClassDefinition
     */
    public function setFortitude($fortitude)
    {
        $this->fortitude = $fortitude;

        return $this;
    }

    /**
     * Get fortitude
     *
     * @return array
     */
    public function getFortitude()
    {
        return $this->fortitude;
    }

    /**
     * Set will
     *
     * @param array $will
     * @return ClassDefinition
     */
    public function setWill($will)
    {
        $this->will = $will;

        return $this;
    }

    /**
     * Get will
     *
     * @return array
     */
    public function getWill()
    {
        return $this->will;
    }

    /**
     * Set spellsPerDay
     *
     * @param array $spellsPerDay
     * @return ClassDefinition
     */
    public function setSpellsPerDay($spellsPerDay)
    {
        $this->spellsPerDay = $spellsPerDay;

        return $this;
    }

    /**
     * Get spellsPerDay
     *
     * @return array
     */
    public function getSpellsPerDay()
    {
        return $this->spellsPerDay;
    }

    /**
     * Add classSkills
     *
     * @param Skill $classSkills
     * @return ClassDefinition
     */
    public function addClassSkill(Skill $classSkills)
    {
        $this->classSkills[] = $classSkills;

        return $this;
    }

    /**
     * Remove classSkills
     *
     * @param Skill $classSkills
     */
    public function removeClassSkill(Skill $classSkills)
    {
        $this->classSkills->removeElement($classSkills);
    }

    /**
     * Get classSkills
     *
     * @return Collection
     */
    public function getClassSkills()
    {
        return $this->classSkills;
    }

    /**
     * @return Collection|SubClass[]
     */
    public function getSubClasses()
    {
        return $this->subClasses;
    }

    /**
     * Add subclass
     *
     * @param SubClass $subClass
     *
     * @return SubClass
     */
    public function addSubClass(SubClass $subClass)
    {
        $this->subClasses[] = $subClass;
        $subClass->setParent($this);

        return $this;
    }

    /**
     * Remove sublcass
     *
     * @param SubClass $subClass
     */
    public function removeSubClass(SubClass $subClass)
    {
        $this->subClasses->removeElement($subClass);
    }

    /**
     * @return int
     */
    public function getSubClassesNumber()
    {
        return $this->subClassesNumber;
    }

    /**
     * @param int $subClassesNumber
     *
     * @return $this
     */
    public function setSubClassesNumber($subClassesNumber)
    {
        $this->subClassesNumber = $subClassesNumber;

        return $this;
    }

    /**
     * Add powers
     *
     * @param ClassPower $power
     *
     * @return ClassDefinition
     */
    public function addPower(ClassPower $power)
    {
        $this->powers[] = $power;
        $power->setClass($this);

        return $this;
    }

    /**
     * Remove powers
     *
     * @param ClassPower $powers
     */
    public function removePower(ClassPower $powers)
    {
        $this->powers->removeElement($powers);
    }

    /**
     * Get powers
     *
     * @param int $level
     *
     * @return Collection|ClassPower[]
     */
    public function getPowers($level = null)
    {
        if ($level) {
            return $this->powers->filter(function (ClassPower $power) use ($level) {
                return $power->getLevel() === $level;
            });
        }

        return $this->powers;
    }

    /**
     * @param string $castingAbility
     *
     * @return $this
     */
    public function setCastingAbility($castingAbility)
    {
        $this->castingAbility = $castingAbility;

        return $this;
    }

    /**
     * @return string
     */
    public function getCastingAbility()
    {
        return $this->castingAbility;
    }

    /**
     * @param boolean $preparationNeeded
     *
     * @return $this
     */
    public function setPreparationNeeded($preparationNeeded)
    {
        $this->preparationNeeded = $preparationNeeded;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPreparationNeeded()
    {
        return $this->preparationNeeded;
    }

    /**
     * @param array $knownSpellsPerLevel
     *
     * @return $this
     */
    public function setKnownSpellsPerLevel($knownSpellsPerLevel)
    {
        $this->knownSpellsPerLevel = $knownSpellsPerLevel;

        return $this;
    }

    /**
     * @return array
     */
    public function getKnownSpellsPerLevel()
    {
        return $this->knownSpellsPerLevel;
    }

    /**
     * Add spell
     *
     * @param ClassSpell $spell
     *
     * @return ClassDefinition
     */
    public function addSpell(ClassSpell $spell)
    {
        foreach ($this->spells as $classSpell) {
            // If already there, simply change its level
            if ($classSpell->getSpell() === $spell->getSpell()) {
                $classSpell->setSpellLevel($spell->getSpellLevel());

                return $this;
            }
        }

        $this->spells[] = $spell;
        $spell->setClass($this);

        return $this;
    }

    /**
     * Remove spell
     *
     * @param ClassSpell $spell
     */
    public function removeSpell(ClassSpell $spell)
    {
        $this->spells->removeElement($spell);
    }

    /**
     * Get spells
     *
     * @return Collection|ClassSpell[]
     */
    public function getSpells()
    {
        return $this->spells;
    }

    /**
     * @return array
     */
    public function getSpellsByLevel()
    {
        $spellsByLevel = array();
        for ($i = 0; $i < 10; $i++) {
            $spellsByLevel[$i] = array();
        }
        foreach($this->getSpells() as $spell) {
            $spellsByLevel[$spell->getSpellLevel()][] = $spell->getSpell();
        }

        return $spellsByLevel;
    }

    /**
     * @param array $spellsByLevel
     *
     * @return $this
     */
    public function setSpellsByLevel($spellsByLevel)
    {
        foreach ($this->spells as $classSpell) {
            $classSpell->setClass();
        }

        $newSpells = new ArrayCollection();
        foreach ($spellsByLevel as $level => $spells) {
            /** @var Spell $spell */
            foreach ($spells as $spell) {
                $olds = $this->spells->filter(function (ClassSpell $s) use ($spell) {
                    return $s->getSpell() === $spell;
                });

                if ($olds->count() === 1) {
                    $oldClassSpell = $olds->first();
                    $oldClassSpell->setClass($this)->setSpellLevel($level);
                    $newSpells->add($oldClassSpell);
                } else {
                    $new = new ClassSpell();
                    $new->setClass($this)->setSpell($spell)->setSpellLevel($level);
                    $newSpells->add($new);
                }
            }
        }

        $this->spells->clear();
        $this->spells = $newSpells;

        return $this;
    }

    /**
     * @param Spell $spell
     *
     * @return ClassSpell|null
     */
    public function getClassSpell(Spell $spell)
    {
        $found = $this->getSpells()->filter(function (ClassSpell $classSpell) use ($spell) {
            return $classSpell->getSpell() === $spell;
        });

        if ($found->count() === 1) {
            return $found->first();
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function isAbleToLearnLowerLevelSpells()
    {
        return $this->ableToLearnLowerLevelSpells;
    }

    /**
     * @param boolean $ableToLearnLowerLevelSpells
     *
     * @return $this
     */
    public function setAbleToLearnLowerLevelSpells($ableToLearnLowerLevelSpells)
    {
        $this->ableToLearnLowerLevelSpells = $ableToLearnLowerLevelSpells;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAbleToLearnNewSpells()
    {
        return $this->ableToLearnNewSpells;
    }

    /**
     * @param boolean $ableToLearnNewSpells
     *
     * @return $this
     */
    public function setAbleToLearnNewSpells($ableToLearnNewSpells)
    {
        $this->ableToLearnNewSpells = $ableToLearnNewSpells;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPrestige()
    {
        return $this->prestige;
    }

    /**
     * @param boolean $prestige
     *
     * @return $this
     */
    public function setPrestige($prestige = false)
    {
        $this->prestige = $prestige;
        return $this;
    }
}
