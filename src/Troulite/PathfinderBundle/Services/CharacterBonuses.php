<?php
/**
 * Created by PhpStorm.
 * User: jean-gui
 * Date: 06/07/14
 * Time: 16:53
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

namespace Troulite\PathfinderBundle\Services;

use Doctrine\ORM\EntityManager;
use Troulite\PathfinderBundle\Entity\Armor;
use Troulite\PathfinderBundle\Entity\Character;
use Troulite\PathfinderBundle\Entity\CharacterClassPower;
use Troulite\PathfinderBundle\Entity\CharacterFeat;
use Troulite\PathfinderBundle\Entity\Item;
use Troulite\PathfinderBundle\Entity\ItemPowerEffect;
use Troulite\PathfinderBundle\Entity\PowerEffect;
use Troulite\PathfinderBundle\Entity\Shield;
use Troulite\PathfinderBundle\Entity\Skill;
use Troulite\PathfinderBundle\Entity\SpellEffect;
use Troulite\PathfinderBundle\Entity\Weapon;
use Troulite\PathfinderBundle\ExpressionLanguage\ExpressionLanguage;
use Troulite\PathfinderBundle\Model\Bonus;
use Troulite\PathfinderBundle\Model\Bonuses;

/**
 * Class CharacterBonuses
 *
 * @package Troulite\PathfinderBundle\Services
 */
class CharacterBonuses
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Character ids whose bonuses have already been applied
     * @var array
     */
    private static $alreadyApplied = array();

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->expressionLanguage = new ExpressionLanguage(null, array(), $this->em);
    }

    /**
     * @param Character $character
     *
     * @return Character
     *
     * @todo bug when some powers enhance abilities and others depend on abilities (ability bonuses should be applied first)
     */
    public function applyAll(Character $character)
    {
        if (in_array($character->getId(), self::$alreadyApplied)) {
            return $character;
        }
        $this->applyRace($character);
        $this->applyItem($character, $character->getEquipment()->getHeadband());
        $this->applyItem($character, $character->getEquipment()->getHead());
        $this->applyItem($character, $character->getEquipment()->getHands());
        $this->applyItem($character, $character->getEquipment()->getLeftFinger());
        $this->applyItem($character, $character->getEquipment()->getRightFinger());
        $this->applyItem($character, $character->getEquipment()->getBelt());
        $this->applyItem($character, $character->getEquipment()->getFeet());
        $this->applyItem($character, $character->getEquipment()->getNeck());
        $this->applyItem($character, $character->getEquipment()->getShoulders());
        $this->applyItem($character, $character->getEquipment()->getWrists());
        $this->applyItem($character, $character->getEquipment()->getArmor());
        $this->applyItem($character, $character->getEquipment()->getMainWeapon());
        if ($character->getEquipment()->getMainWeapon() !== $character->getEquipment()->getOffhandWeapon()) {
            $this->applyItem($character, $character->getEquipment()->getOffhandWeapon());
        }

        // Malus if dual-wielding
        if ($character->isDualWielding()) {
            if ($character->getEquipment()->getOffhandWeapon()->isLight()) {
                $malus = [
                    'main-attack-roll'    => ['type' => null, 'value' => -4],
                    'offhand-attack-roll' => ['type' => null, 'value' => -8],
                ];

            }
            else {
                $malus = [
                    'main-attack-roll'    => ['type' => null, 'value' => -6],
                    'offhand-attack-roll' => ['type' => null, 'value' => -10],
                ];
            }
            $this->applyEffects($character, $malus, $character->getEquipment()->getOffhandWeapon());
        }

        $this->applyFeats($character);
        $this->applyClassPowers($character);
        $this->applySpellEffects($character);
        $this->applyPowerEffects($character);
        $this->applyItemPowerEffects($character);

        self::$alreadyApplied[] = $character->getId();

        return $character;
    }

    /**
     * Apply racial traits to a character
     * @param Character $character
     */
    private function applyRace(Character $character)
    {
        $this->applyEffects(
            $character,
            $character->getRace()->getTraits(),
            $character->getRace()
        );
    }

    /**
     * Apply all active feats to a character
     *
     * @param Character $character
     *
     * @return Character
     */
    private function applyFeats(Character $character)
    {
        foreach ($character->getFeats() as $feat) {
            if ($this->isApplicable($feat)) {
                $this->applyPower($character, $feat->getFeat());
            }
        }

        return $character;
    }

    /**
     * @param Character $character
     *
     * @return Character
     */
    private function applyClassPowers(Character $character)
    {
        foreach ($character->getClassPowers() as $classPower) {
            if ($this->isApplicable($classPower)) {
                $this->applyPower($character, $classPower->getClassPower());
            }
        }

        return $character;
    }

    /**
     * @param Character $character
     *
     * @return Character
     */
    private function applySpellEffects(Character $character)
    {
        foreach ($character->getSpellEffects() as $spellEffect) {
            if ($this->isApplicable($spellEffect)) {
                $this->applySpellEffect($character, $spellEffect);
            }
        }

        return $character;
    }

    /**
     * @param Character $character
     * @param SpellEffect $spellEffect
     *
     * @return Character
     *
     * @todo Properly handle cases where spell effects depend on caster level
     */
    private function applySpellEffect(Character $character, SpellEffect $spellEffect)
    {
        $effects = array();
        foreach ($spellEffect->getSpell()->getEffects() as $stat => $effect) {
            $computedEffect = (int)$this->expressionLanguage->evaluate(
                (string)($effect['value']),
                array(
                    'caster' => $spellEffect->getCaster(),
                    'level'  => $spellEffect->getCasterLevel()
                )
            );
            $effects[$stat] = ['type' => $effect['type'], 'value' => $computedEffect];
        }
        return $this->applyEffects($character, $effects, $spellEffect);
    }

    /**
     * @param Character $character
     *
     * @return Character
     */
    private function applyPowerEffects(Character $character)
    {
        foreach ($character->getPowerEffects() as $powerEffect) {
            if ($this->isApplicable($powerEffect)) {
                $this->applyPowerEffect($character, $powerEffect);
            }
        }

        return $character;
    }

    /**
     * @param Character $character
     *
     * @return Character
     */
    private function applyItemPowerEffects(Character $character)
    {
        foreach ($character->getItemPowerEffects() as $powerEffect) {
            if ($this->isApplicable($powerEffect)) {
                $this->applyItemPowerEffect($character, $powerEffect);
            }
        }

        return $character;
    }

    /**
     * @param Character $character
     * @param PowerEffect $powerEffect
     *
     * @return Character
     */
    private function applyPowerEffect(Character $character, PowerEffect $powerEffect)
    {
        $effects = array();
        foreach ($powerEffect->getPower()->getEffects() as $stat => $effect) {
            $computedEffect = (int)$this->expressionLanguage->evaluate(
                (string)($effect['value']),
                array(
                    'caster' => $powerEffect->getCaster(),
                    'level'  => $powerEffect->getCasterLevel()
                )
            );
            $effects[$stat] = ['type' => $effect['type'], 'value' => $computedEffect];
        }

        return $this->applyEffects($character, $effects, $powerEffect);
    }

    /**
     * @param Character $character
     * @param ItemPowerEffect $itemPowerEffect
     *
     * @return Character
     */
    private function applyItemPowerEffect(Character $character, ItemPowerEffect $itemPowerEffect)
    {
        $effects = array();
        foreach ($itemPowerEffect->getPower()->getEffects() as $stat => $effect) {
            $computedEffect = (int)$this->expressionLanguage->evaluate(
                (string)($effect['value']),
                array(
                    'c' => $itemPowerEffect->getCharacter(),
                )
            );
            $effects[$stat] = ['type' => $effect['type'], 'value' => $computedEffect];
        }

        return $this->applyEffects($character, $effects, $itemPowerEffect);
    }

    /**
     * Return whether a feat's bonuses can be applied
     *
     * @param mixed $characterPower
     *
     * @return bool
     */
    private function isApplicable($characterPower)
    {
        /** @var $character Character */
        $character = $characterPower->getCharacter();
        $power = null;
        if ($characterPower instanceof CharacterFeat) {
            if ($characterPower->getFeat()->getId() == 204) {
                $foo = true;
            }
            $power = $characterPower->getFeat();
        } elseif ($characterPower instanceof CharacterClassPower) {
            $power = $characterPower->getClassPower();
            // Don't apply castable powers
            if ($power->isCastable()) {
                return false;
            }
        } elseif ($characterPower instanceof SpellEffect) {
            $power = $characterPower->getSpell();
        } elseif ($characterPower instanceof PowerEffect) {
            $power = $characterPower->getPower();
        } elseif ($characterPower instanceof ItemPowerEffect) {
            $power = $characterPower->getPower();
        }

        if ($power === null) {
            return false;
        }

        foreach ($power->getConditions() as $type => $condition) {
            switch($type) {
                case 'weapon-type':
                    $weapon = $character->getEquipment()->getMainWeapon();
                    if (!$weapon) {
                        return false;
                    }
                    if (is_array($condition)) {
                        if (
                            in_array($weapon->getType(), $condition) ||
                            (in_array('light-weapon', $condition) && $weapon->isLight())
                        ) {
                            // continue
                        } else {
                            return false;
                        }
                    }
                    if (($condition === 'light-weapon' && !$weapon->isLight()) && $weapon->getType() !== $condition) {
                        return false;
                    }
                    break;
                case 'equipped':
                    $mainHand = $character->getEquipment()->getMainWeapon();
                    $offHand  = $character->getEquipment()->getOffhandWeapon();
                    switch ($condition) {
                        case 'shield':
                            return ($mainHand instanceof Shield) || ($offHand instanceof Shield);
                        case 'dual-wielding':
                            return ($mainHand instanceof Weapon) && ($offHand instanceof Weapon);
                    }
                    break;
            }
        }

        return ($power->isPassive() && !$power->hasExternalConditions()) || $characterPower->isActive();
    }

    /**
     * Apply feat effects to a character
     *
     * @param Character $character
     * @param mixed $power anything with the Power trait
     *
     * @return Character
     */
    private function applyPower(Character $character, $power)
    {
        return $this->applyEffects($character, $power->getEffects(), $power);
    }

    /**
     * Apply item effects to a character
     *
     * @param Character $character
     * @param Item|null $item
     *
     * @return Character
     */
    private function applyItem(Character $character, Item $item = null)
    {
        if ($item) {
            if ($item instanceof Armor) {
                $this->applyEffects(
                    $character,
                    array('ac' => array('type' => 'armor', 'value' => $item->getAc())),
                    $item
                );
            }

            if ($item instanceof Shield) {
                $this->applyEffects(
                    $character,
                    array('ac' => array('type' => 'shield', 'value' => $item->getAc())),
                    $item
                );
            }

            foreach ($item->getPowers() as $power) {
                if ($power->isPassive() && !$power->hasExternalConditions()) {
                    $this->applyEffects($character, $power->getEffects(), $item);
                }
            }

            // Armor check penalty
            if ($item instanceof Armor or $item instanceof Shield) {
                /** @var $item Armor|Shield */
                $skills = $this->em->getRepository('TroulitePathfinderBundle:Skill')->findAll();

                $effects = array();
                /** @var Skill $skill */
                foreach ($skills as $skill) {
                    if ($skill->getArmorCheckPenalty()) {
                        $effects[$skill->getShortname()] = array(
                            'type' => null,
                            'value' => $item->getArmorCheckPenalty()
                        );
                    }
                }
                $this->applyEffects($character, $effects, $item);
            }
        }

        return $character;
    }

    /**
     * Apply effects to a character
     *
     * @param Character $character
     * @param array $effects
     * @param mixed $source
     *
     * @return Character
     */
    private function applyEffects(Character $character, array $effects, $source)
    {
        foreach ($effects as $stat => $effect) {
            if ($stat === 'choice' || $stat === 'feat' || $stat === 'extra-feats') {
                // Don't process effects that apply when leveling up
                continue;
            }
            $value = (int)$this->expressionLanguage->evaluate(
                $effect['value'],
                array("c" => $character)
            );

            // No need to add empty bonuses
            if ($value == 0) {
                continue;
            }

            $type = $effect['type'];
            $bonus = new Bonus($source, $value, $type);

            switch ($stat) {
                case 'strength':
                    $character->getAbilitiesBonuses()->strength->add($bonus);
                    break;
                case 'dexterity':
                    $character->getAbilitiesBonuses()->dexterity->add($bonus);
                    break;
                case 'constitution':
                    $character->getAbilitiesBonuses()->constitution->add($bonus);
                    break;
                case 'intelligence':
                    $character->getAbilitiesBonuses()->intelligence->add($bonus);
                    break;
                case 'wisdom':
                    $character->getAbilitiesBonuses()->wisdom->add($bonus);
                    break;
                case 'charisma':
                    $character->getAbilitiesBonuses()->charisma->add($bonus);
                    break;
                case 'max-dexterity-bonus':
                    $character->getAbilitiesBonuses()->maxDexterityBonuses->add($bonus);
                    break;
                case 'fortitude':
                    $character->getDefenseBonuses()->fortitude->add($bonus);
                    break;
                case 'reflexes':
                    $character->getDefenseBonuses()->reflexes->add($bonus);
                    break;
                case 'will':
                    $character->getDefenseBonuses()->will->add($bonus);
                    break;
                case 'initiative':
                    $character->getAttackBonuses()->initiative->add($bonus);
                    break;
                case 'ac':
                    $character->getDefenseBonuses()->ac->add($bonus);
                    break;
                case 'spell-resistance':
                    $character->getDefenseBonuses()->spellResitance->add($bonus);
                    break;
                case 'cmb':
                    $character->getAttackBonuses()->cmb->add($bonus);
                    break;
                case 'cmd':
                    $character->getAttackBonuses()->cmd->add($bonus);
                    break;
                case 'main-attack-roll':
                    $character->getAttackBonuses()->mainAttackRolls->add($bonus);
                    break;
                case 'main-damage-roll':
                    $character->getAttackBonuses()->mainDamage->add($bonus);
                    break;
                case 'main-attacks':
                    $character->getAttackBonuses()->mainAttacks->add($bonus);
                    break;
                case 'offhand-attack-roll':
                    $character->getAttackBonuses()->offhandAttackRolls->add($bonus);
                    break;
                case 'offhand-damage-roll':
                    $character->getAttackBonuses()->offhandDamage->add($bonus);
                    break;
                case 'offhand-attacks':
                    $character->getAttackBonuses()->offhandAttacks->add($bonus);
                    break;
                case 'melee-attack-roll':
                    $character->getAttackBonuses()->meleeAttackRolls->add($bonus);
                    break;
                case 'melee-damage-roll':
                    $character->getAttackBonuses()->meleeDamage->add($bonus);
                    break;
                case 'melee-attacks':
                    $character->getAttackBonuses()->meleeAttacks->add($bonus);
                    break;
                case 'ranged-attack-roll':
                    $character->getAttackBonuses()->rangedAttackRolls->add($bonus);
                    break;
                case 'ranged-damage-roll':
                    $character->getAttackBonuses()->rangedDamage->add($bonus);
                    break;
                case 'ranged-attacks':
                    $character->getAttackBonuses()->rangedAttacks->add($bonus);
                    break;
                case 'attack-roll':
                    $character->getAttackBonuses()->mainAttackRolls->add($bonus);
                    $character->getAttackBonuses()->offhandAttackRolls->add($bonus);
                    break;
                case 'damage-roll':
                    $character->getAttackBonuses()->mainDamage->add($bonus);
                    $character->getAttackBonuses()->offhandDamage->add($bonus);
                    break;
                case 'wielded-attack-roll':
                    if ($bonus->getSource() === $character->getEquipment()->getMainWeapon()) {
                        $character->getAttackBonuses()->mainAttackRolls->add($bonus);
                        if ($character->getEquipment()->getOffhandWeapon() === $bonus->getSource()) {
                            $character->getAttackBonuses()->offhandAttackRolls->add($bonus);
                        }
                    }

                    if (
                        $bonus->getSource() === $character->getEquipment()->getOffhandWeapon() &&
                        $character->getEquipment()->getOffhandWeapon() !== $bonus->getSource()
                    ) {
                        $character->getAttackBonuses()->offhandAttackRolls->add($bonus);
                    }
                    // if wielded-attack-roll is on anything other than a weapon, it is lost
                    break;
                case 'wielded-damage-roll':
                    if ($bonus->getSource() === $character->getEquipment()->getMainWeapon()) {
                        $character->getAttackBonuses()->mainDamage->add($bonus);
                        if ($character->getEquipment()->getOffhandWeapon() === $bonus->getSource()) {
                            $character->getAttackBonuses()->offhandDamage->add($bonus);
                        }
                    }

                    if (
                        $bonus->getSource() === $character->getEquipment()->getOffhandWeapon() &&
                        $character->getEquipment()->getOffhandWeapon() !== $bonus->getSource()
                    ) {
                        $character->getAttackBonuses()->offhandDamage->add($bonus);
                    }
                    break;
                case 'acrobatics':
                case 'appraise':
                case 'bluff':
                case 'climb':
                case 'craft':
                case 'diplomacy':
                case 'disable-device':
                case 'disguise':
                case 'escape-artist':
                case 'fly':
                case 'handle-animal':
                case 'heal':
                case 'intimidate':
                case 'knowledge-arcana':
                case 'knowledge-dungeoneering':
                case 'knowledge-engineering':
                case 'knowledge-geography':
                case 'knowledge-history':
                case 'knowledge-local':
                case 'knowledge-nature':
                case 'knowledge-nobility':
                case 'knowledge-planes':
                case 'knowledge-religion':
                case 'linguistics':
                case 'perception':
                case 'perform':
                case 'profession':
                case 'ride':
                case 'sense-motive':
                case 'sleight-of-hand':
                case 'spellcraft':
                case 'stealth':
                case 'survival':
                case 'swim':
                case 'use-magic-device':
                    if (!array_key_exists($stat, $character->getSkillsBonuses())) {
                        $character->getSkillsBonuses()[$stat] = new Bonuses();
                    }
                    /** @var $bonuses Bonuses */
                    $bonuses = $character->getSkillsBonuses()[$stat];
                    $bonuses->add($bonus);
                    break;
                case 'hp':
                    $character->getHpBonuses()->add($bonus);
                    break;
                case 'speed':
                    $character->getSpeedBonuses()->add($bonus);
                    break;
            }
        }

        return $character;
    }
} 