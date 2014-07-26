<?php
/**
 * Created by PhpStorm.
 * User: jean-gui
 * Date: 06/07/14
 * Time: 19:04
 */

namespace Troulite\PathfinderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Troulite\PathfinderBundle\Model\AbilitiesBonuses;
use Troulite\PathfinderBundle\Model\AttackBonuses;
use Troulite\PathfinderBundle\Model\Bonus;
use Troulite\PathfinderBundle\Model\Bonuses;
use Troulite\PathfinderBundle\Model\DefenseBonuses;

/**
 * Class BaseCharacter
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 * @package Troulite\PathfinderBundle\Entity
 */
class Character extends BaseCharacter
{
    /**
     * @var Collection|Level[]
     *
     * @ORM\OneToMany(targetEntity="Level", mappedBy="character", cascade={"all"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $levels;

    /**
     * @var AbilitiesBonuses
     */
    public $abilitiesBonuses;

    /**
     * @var AttackBonuses
     */
    private $attackBonuses;

    /**
     * @var DefenseBonuses
     */
    private $defenseBonuses;

    /**
     * @var Bonuses
     */
    private $hpBonuses;

    /**
     * @var array
     */
    private $skillsBonuses;

    /**
     * @var Bonuses
     */
    private $speedBonuses;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->postLoad();
        $this->levels = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->abilitiesBonuses = new AbilitiesBonuses();
        $this->defenseBonuses   = new DefenseBonuses();
        $this->attackBonuses    = new AttackBonuses();
        $this->hpBonuses        = new Bonuses();
        $this->skillsBonuses    = array();
        $this->speedBonuses     = new Bonuses();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Add level
     *
     * @param Level $level
     *
     * @return $this
     */
    public function addLevel(Level $level)
    {
        $level->setCharacter($this);
        $this->levels[] = $level;

        return $this;
    }

    /**
     * Remove level
     *
     * @param Level $level
     */
    public function removeLevel(Level $level)
    {
        $level->setCharacter(null);
        $this->levels->removeElement($level);
    }

    /**
     * Get level
     *
     * @return Collection|Level[]
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @return AbilitiesBonuses
     */
    public function getAbilitiesBonuses()
    {
        return $this->abilitiesBonuses;
    }

    /**
     * @param AbilitiesBonuses $abilitiesBonuses
     */
    public function setAbilitiesBonuses($abilitiesBonuses)
    {
        $this->abilitiesBonuses = $abilitiesBonuses;
    }

    /**
     * @param Bonuses $hpBonuses
     *
     * @return $this
     */
    public function setHpBonuses(Bonuses $hpBonuses)
    {
        $this->hpBonuses = $hpBonuses;

        return $this;
    }

    /**
     * @return Bonuses
     */
    public function getHpBonuses()
    {
        return $this->hpBonuses;
    }

    /**
     * @param array $skillsBonuses
     */
    public function setSkillsBonuses($skillsBonuses)
    {
        $this->skillsBonuses = $skillsBonuses;
    }

    /**
     * @return array
     */
    public function &getSkillsBonuses()
    {
        return $this->skillsBonuses;
    }

    /**
     * @param AttackBonuses $attackBonuses
     */
    public function setAttackBonuses($attackBonuses)
    {
        $this->attackBonuses = $attackBonuses;
    }

    /**
     * @return AttackBonuses
     */
    public function getAttackBonuses()
    {
        return $this->attackBonuses;
    }

    /**
     * @param DefenseBonuses $defenseBonuses
     *
     * @return $this
     */
    public function setDefenseBonuses(DefenseBonuses $defenseBonuses)
    {
        $this->defenseBonuses = $defenseBonuses;

        return $this;
    }

    /**
     * @return DefenseBonuses
     */
    public function getDefenseBonuses()
    {
        return $this->defenseBonuses;
    }

    /**
     * @param Skill $skill
     *
     * @return int
     */
    public function getSkillRank(Skill $skill)
    {
        $rank = 0;
        foreach ($this->getLevels() as $level) {
            foreach ($level->getSkills() as $levelSkill) {
                if ($levelSkill->getSkill() === $skill) {
                    $rank += $levelSkill->getValue();
                    break;
                }
            }
        }

        return $rank;
    }

    /**
     * @param Skill $skill
     *
     * @return bool
     */
    public function hasClassBonus(Skill $skill)
    {
        foreach ($this->getLevels() as $level) {
            if ($level->getClassDefinition()->getClassSkills()->contains($skill)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRangedAttackRoll()
    {
        return $this->getAttackRoll("ranged", $this->getAbilityModifier($this->getDexterity()));
    }

    /**
     * @return int
     */
    public function getMeleeDamageRoll()
    {
        return $this->attackBonuses->meleeDamage->getBonus() + $this->getAbilityModifier($this->getStrength());
    }

    /**
     * @return int
     */
    public function getRangedDamageRoll()
    {
        return $this->attackBonuses->rangedDamage->getBonus();
    }

    /**
     * Get strength
     *
     * @return integer
     */
    public function getStrength()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::STRENGTH) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseStrength() +
            $this->getAbilitiesBonuses()->strength->getBonus() +
            $levelBonus;
    }

    /**
     * Get dexterity
     *
     * @return integer
     */
    public function getDexterity()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::DEXTERITY) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseDexterity() +
            $this->getAbilitiesBonuses()->dexterity->getBonus() +
            $levelBonus;
    }

    /**
     * Get constitution
     *
     * @return integer
     */
    public function getConstitution()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::CONSTITUTION) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseConstitution() +
            $this->getAbilitiesBonuses()->constitution->getBonus() +
            $levelBonus;
    }

    /**
     * Get intelligence
     *
     * @return integer
     */
    public function getIntelligence()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::INTELLIGENCE) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseIntelligence() +
            $this->getAbilitiesBonuses()->intelligence->getBonus() +
            $levelBonus;
    }

    /**
     * Get wisdom
     *
     * @return integer
     */
    public function getWisdom()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::WISDOM) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseWisdom() +
            $this->getAbilitiesBonuses()->wisdom->getBonus() +
            $levelBonus;
    }

    /**
     * Get charisma
     *
     * @return integer
     */
    public function getCharisma()
    {
        $levelBonus = 0;
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::CHARISMA) {
                $levelBonus += 1;
            }
        }

        return
            $this->getAbilities()->getBaseCharisma() +
            $this->getAbilitiesBonuses()->charisma->getBonus() +
            $levelBonus;
    }

    /**
     * @return array
     */
    public function getMeleeAttackRoll()
    {
        return $this->getAttackRoll("melee", $this->getAbilityModifier($this->getStrength()));
    }

    /**
     * @param $type
     * @param $modifier
     *
     * @return array
     */
    private function getAttackRoll($type, $modifier)
    {
        $bab          = $this->getBab();
        $ar           = $bab + $modifier;
        $bonusAttacks = 0;
        $ars          = array();

        switch ($type) {
            case 'ranged':
                $ar += $this->attackBonuses->rangedAttackRolls->getBonus();
                $bonusAttacks = $this->attackBonuses->rangedAttacks->getBonus();
                break;
            case 'melee':
                $ar += $this->attackBonuses->meleeAttackRolls->getBonus();
                $bonusAttacks = $this->attackBonuses->meleeAttacks->getBonus();
        }

        /** @noinspection PhpExpressionResultUnusedInspection */
        for ($bonusAttacks; $bonusAttacks > 0; $bonusAttacks--) {
            $ars[] = $ar;
        }
        /** @noinspection PhpExpressionResultUnusedInspection */
        for ($bab; $bab >= 0; $bab -= 5) {
            $ars[] = $ar;
            $ar -= 5;
        }

        return $ars;
    }

    /**
     * @return int
     */
    public function getBab()
    {
        $bab = 0;
        foreach ($this->getLevelPerClass() as $classLevel) {
            /** @var $class ClassDefinition */
            $class = $classLevel['class'];
            $bab += $class->getBab()[$classLevel['level'] - 1];
        }

        return $bab;
    }

    /**
     * Get this character's maximumhit points
     *
     * @return int
     */
    public function getMaxHp()
    {
        $hp = $this->getHpBonuses()->getBonus();
        foreach ($this->getLevels() as $level) {
            $hp += $level->getHpRoll() + $this->getAbilityModifier($this->getConstitution());

            // Extra hit point if favored class
            if ($this->getExtraPoint() === 'hp' && $level->isFavoredClass()) {
                $hp += 1;
            }
        }

        return $hp;
    }

    /**
     * @return array
     */
    public function getLevelPerClass()
    {
        /** @var $levels array */
        $levels = array();
        foreach ($this->getLevels() as $level) {
            $class = $level->getClassDefinition();
            if ($class) {
                if (array_key_exists($class->getId(), $levels)) {
                    $levels[$class->getId()]['level']++;
                } else {
                    $levels[$class->getId()] = array(
                        'class' => $class,
                        'level' => 1
                    );
                }
            }
        }

        return $levels;
    }

    /**
     * Get the ability modifier corresponding to the value of the argument
     *
     * @param int $value
     *
     * @return int
     */
    public function getAbilityModifier($value)
    {
        return (int)(($value - ($value % 2) - 10) / 2);
    }

    /**
     * Get base reflexes
     *
     * @return int
     */
    public function getBaseReflexes()
    {
        $reflexes = 0;
        foreach ($this->getLevelPerClass() as $classLevel) {
            /** @var $class ClassDefinition */
            $class = $classLevel['class'];
            $reflexes += $class->getReflexes()[$classLevel['level'] - 1];
        }

        return $reflexes;
    }

    /**
     * Get reflexes (sum of base reflexes + dexterity modifier + bonuses
     *
     * @return int
     */
    public function getReflexes()
    {
        return $this->getBaseReflexes()
        + $this->getAbilityModifier($this->getDexterity())
        + $this->getDefenseBonuses()->reflexes->getBonus();
    }

    /**
     * Get base fortitude
     *
     * @return int
     */
    public function getBaseFortitude()
    {
        $fortitude = 0;
        foreach ($this->getLevelPerClass() as $classLevel) {
            /** @var $class ClassDefinition */
            $class = $classLevel['class'];
            $fortitude += $class->getFortitude()[$classLevel['level'] - 1];
        }

        return $fortitude;
    }

    /**
     * Get fortitude (sum of base fortitude + constitution modifier + bonuses
     *
     * @return int
     */
    public function getFortitude()
    {
        return $this->getBaseFortitude()
        + $this->getAbilityModifier($this->getConstitution())
        + $this->getDefenseBonuses()->fortitude->getBonus();
    }

    /**
     * Get base will
     *
     * @return int
     */
    public function getBaseWill()
    {
        $will = 0;
        foreach ($this->getLevelPerClass() as $classLevel) {
            /** @var $class ClassDefinition */
            $class = $classLevel['class'];
            $will += $class->getWill()[$classLevel['level'] - 1];
        }

        return $will;
    }

    /**
     * Get will (sum of base will + wisdom modifier + bonuses
     *
     * @return int
     */
    public function getWill()
    {
        return $this->getBaseWill()
        + $this->getAbilityModifier($this->getWisdom())
        + $this->getDefenseBonuses()->will->getBonus();
    }

    /**
     * Get this character's current level
     *
     * @param ClassDefinition $class
     *
     * @return int
     */
    public function getLevel(ClassDefinition $class = null)
    {
        if ($class) {
            return $this->getLevelPerClass()[$class->getId()]['level'];
        }

        return $this->getLevels()->count();
    }

    /**
     * @param $skill
     * @param $value
     */
    public function setSkillBonus($skill, $value)
    {
        $this->skillsBonuses[$skill] = $value;
    }

    /**
     * @param $skill
     *
     * @return int
     */
    public function getSkillBonus($skill)
    {
        if (array_key_exists($skill, $this->getSkillsBonuses())) {
            return $this->getSkillsBonuses()[$skill];
        }

        return 0;
    }

    /**
     * @param Bonuses $speedBonuses
     */
    public function setSpeedBonuses($speedBonuses)
    {
        $this->speedBonuses = $speedBonuses;
    }

    /**
     * @return Bonuses
     */
    public function getSpeedBonuses()
    {
        return $this->speedBonuses;
    }

    /**
     * Return all feats this character possesses
     * @return ArrayCollection|CharacterFeat[]
     */
    public function getFeats()
    {
        $feats = array();
        foreach($this->getLevels() as $level) {
            $feats = array_merge($feats, $level->getFeats()->toArray());
        }

        return new ArrayCollection($feats);
    }

    /**
     * Return all class powers this character possesses
     *
     * @return Collection|CharacterClassPower[]
     */
    public function getClassPowers()
    {
        $classPowers = array();
        foreach ($this->getLevels() as $level) {
            $classPowers = array_merge($classPowers, $level->getClassPowers()->toArray());
        }

        return new ArrayCollection($classPowers);
    }

    /**
     * @todo dexterity modifier limited by armor
     * @todo bonuses
     * @return int
     */
    public function getAc()
    {
        return
            10 +
            min(
                $this->getAbilityModifier($this->getDexterity()),
                $this->getEquipment()->getBody()->getMaximumDexterityBonus()
            ) +
            $this->getDefenseBonuses()->ac->getBonus();
    }

    /**
     * @todo bonuses
     * @return int
     */
    public function getTouchAc()
    {
        /** @var $dodgeBonuses Bonus[] */
        $dodgeBonuses = array_filter(
            $this->getDefenseBonuses()->ac->getBonuses(),
            function (Bonus $bonus) {
                return $bonus->getType() === 'dodge';
            }
        );
        $dodgeBonus   = 0;
        foreach ($dodgeBonuses as $db) {
            $dodgeBonus += $db->getValue();
        }
        return 10 + $this->getAbilityModifier($this->getDexterity()) + $dodgeBonus;
    }

    /**
     * Flat-footed AC doesn't take dexterity or dodge bonus
     * @return int
     */
    public function getFlatFootedAc()
    {
        /** @var $dodgeBonuses Bonus[] */
        $dodgeBonuses = array_filter(
            $this->getDefenseBonuses()->ac->getBonuses(),
            function(Bonus $bonus) {
                return $bonus->getType() === 'dodge';
            }
        );
        $dodgeBonus = 0;
        foreach ($dodgeBonuses as $db) {
            $dodgeBonus += $db->getValue();
        }
        return 10 + $this->getDefenseBonuses()->ac->getBonus() - $dodgeBonus;
    }

    /**
     * @return int
     */
    public function getInitiative()
    {
        return $this->getAbilityModifier($this->getDexterity()) +
            $this->getAttackBonuses()->initiative->getBonus();
    }

    /**
     * @param string $ability
     *
     * @return int
     */
    public function getModifierByAbility($ability)
    {
        switch ($ability) {
            case 'strength':
                return $this->getAbilityModifier($this->getStrength());
            case 'dexterity':
                return $this->getAbilityModifier($this->getDexterity());
            case 'constitution':
                return $this->getAbilityModifier($this->getConstitution());
            case 'intelligence':
                return $this->getAbilityModifier($this->getIntelligence());
            case 'wisdom':
                return $this->getAbilityModifier($this->getWisdom());
            case 'charisma':
                return $this->getAbilityModifier($this->getCharisma());
        }
        return 0;
    }

    /**
     * @return int
     */
    public function getAvailableSkillPoints()
    {
        $available = 0;

        // Intelligence modifier (profile only).
        $intelligence = $this->getAbilities()->getBaseIntelligence();
        foreach ($this->getLevels() as $level) {
            if ($level->getExtraAbility() == Abilities::INTELLIGENCE) {
                $intelligence++;
            }
        }
        // Racial intelligence bonuses
        foreach ($this->getAbilitiesBonuses()->intelligence->getBonuses() as $bonus) {
            if ($bonus->getType() == 'racial') {
                $intelligence += $bonus->getValue();
            }
        }

        foreach ($this->getLevels() as $level) {
            $levelPoints = $level->getClassDefinition()->getSkillPoints();

            // Add Intelligence mod. Available points can't be < 1 after applying intelligence
            $levelPoints += $this->getAbilityModifier($intelligence);
            if ($levelPoints < 1) {
                $levelPoints = 1;
            }

            // Add skill bonus for favored classes
            if (
                $level->getClassDefinition() === $this->getFavoredClass() &&
                $this->getExtraPoint() === 'skill'
            ) {
                $levelPoints++;
            }

            // Racial Bonus
            $traits = $this->getRace()->getTraits();
            if (array_key_exists('extra_skills_per_level', $traits)) {
                $levelPoints += $traits['extra_skills_per_level']['value'];
            }

            // Subtract spent points
            foreach ($level->getSkills() as $levelSkill) {
                $levelPoints -= $levelSkill->getValue();
            }

            $available += $levelPoints;
        }

        return $available;
    }
}