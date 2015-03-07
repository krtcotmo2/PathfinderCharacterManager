<?php
/**
 * Created by PhpStorm.
 * User: jean-gui
 * Date: 06/03/15
 * Time: 14:08
 */

namespace Troulite\PathfinderBundle\Model;


use Troulite\PathfinderBundle\Entity\ClassDefinition;
use Troulite\PathfinderBundle\Entity\Spell;

/**
 * Class CastableClassSpells
 *
 * @package Troulite\PathfinderBundle\Model
 */
class CastableClassSpells {
    /**
     * @var ClassDefinition
     */
    private $class;

    /**
     * @var CastableLevelSpells[]
     */
    private $spellsByLevel;

    /**
     *
     */
    public function __construct()
    {
        $this->spellsByLevel = array();
    }

    /**
     * @return ClassDefinition
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param ClassDefinition $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return CastableLevelSpells[]
     */
    public function getSpellsByLevel()
    {
        return $this->spellsByLevel;
    }

    /**
     * @param CastableLevelSpells[] $spellsByLevel
     *
     * @return $this
     */
    public function setSpellsByLevel($spellsByLevel)
    {
        $this->spellsByLevel = $spellsByLevel;

        return $this;
    }

    /**
     * @param Spell $spell
     * @param int $level
     *
     * @return $this
     */
    public function addSpellToLevel(Spell $spell, $level)
    {
        if (!array_key_exists($level, $this->spellsByLevel)) {
            $this->spellsByLevel[$level] = new CastableLevelSpells();
            $this->spellsByLevel[$level]->setLevel($level);
        }
        $this->spellsByLevel[$level]->addSpell($spell);

        return $this;
    }
}