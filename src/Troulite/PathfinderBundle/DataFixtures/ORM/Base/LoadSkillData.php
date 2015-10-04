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

namespace Troulite\PathfinderBundle\DataFixtures\ORM\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Troulite\PathfinderBundle\Entity\Skill;

/**
 * Class LoadSkillData
 *
 * @package Troulite\PathfinderBundle\DataFixtures\ORM\Base
 */
class LoadSkillData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $acrobatics = new Skill();
        $acrobatics
            ->setName('Acrobatics')
            ->setShortname('acrobatics')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('dexterity');

        $appraise = new Skill();
        $appraise
            ->setName('Appraise')
            ->setShortname('appraise')
            ->setUntrained(true)
            ->setKeyAbility('intelligence');
        $bluff = new Skill();
        $bluff
            ->setName('Bluff')
            ->setShortname('bluff')
            ->setKeyAbility('charisma');
        $climb = new Skill();
        $climb
            ->setName('Climb')
            ->setShortname('climb')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('strength');
        $craft = new Skill();
        $craft
            ->setName('Craft')
            ->setShortname('craft')
            ->setKeyAbility('intelligence');
        $diplomacy = new Skill();
        $diplomacy
            ->setName('Diplomacy')
            ->setShortname('diplomacy')
            ->setKeyAbility('charisma');
        $disableDevice = new Skill();
        $disableDevice
            ->setName('Disable Device')
            ->setShortname('disable-device')
            ->setArmorCheckPenalty(true)
            ->setUntrained(false)
            ->setKeyAbility('dexterity');
        $disguise = new Skill();
        $disguise
            ->setName('Disguise')
            ->setShortname('disguise')
            ->setKeyAbility('charisma');
        $escapeArtist = new Skill();
        $escapeArtist
            ->setName('Escape Artist')
            ->setShortname('escape-artist')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('dexterity');
        $fly = new Skill();
        $fly
            ->setName('Fly')
            ->setShortname('fly')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('dexterity');
        $handleAnimal = new Skill();
        $handleAnimal
            ->setName('Handle Animal')
            ->setShortname('handle-animal')
            ->setUntrained(false)
            ->setKeyAbility('charisma');
        $heal = new Skill();
        $heal
            ->setName('Heal')
            ->setShortname('heal')
            ->setKeyAbility('wisdom');
        $intimidate = new Skill();
        $intimidate
            ->setName('Intimidate')
            ->setShortname('intimidate')
            ->setKeyAbility('charisma');
        $knowledgeArcana = new Skill();
        $knowledgeArcana
            ->setName('Knowledge (Arcana)')
            ->setShortname('knowledge-arcana')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeDungeoneering = new Skill();
        $knowledgeDungeoneering
            ->setName('Knowledge (Dungeoneering)')
            ->setShortname('knowledge-dungeoneering')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeEngineering = new Skill();
        $knowledgeEngineering
            ->setName('Knowledge (Engineering)')
            ->setShortname('knowledge-engineering')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeGeography = new Skill();
        $knowledgeGeography
            ->setName('Knowledge (Geography)')
            ->setShortname('knowledge-geography')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeHistory = new Skill();
        $knowledgeHistory
            ->setName('Knowledge (History)')
            ->setShortname('knowledge-history')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeLocal = new Skill();
        $knowledgeLocal
            ->setName('Knowledge (Local)')
            ->setShortname('knowledge-local')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeNature = new Skill();
        $knowledgeNature
            ->setName('Knowledge (Nature)')
            ->setShortname('knowledge-nature')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeNobility = new Skill();
        $knowledgeNobility
            ->setName('Knowledge (Nobility)')
            ->setShortname('knowledge-nobility')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgePlanes = new Skill();
        $knowledgePlanes
            ->setName('Knowledge (Planes)')
            ->setShortname('knowledge-planes')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $knowledgeReligion = new Skill();
        $knowledgeReligion
            ->setName('Knowledge (Religion)')
            ->setShortname('knowledge-religion')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $linguistics = new Skill();
        $linguistics
            ->setName('Linguistics')
            ->setShortname('knowledge-linguistics')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $perception = new Skill();
        $perception
            ->setName('Perception')
            ->setShortname('perception')
            ->setKeyAbility('wisdom');
        $perform = new Skill();
        $perform
            ->setName('Perform')
            ->setShortname('perform')
            ->setKeyAbility('charisma');
        $profession = new Skill();
        $profession
            ->setName('Profession')
            ->setShortname('profession')
            ->setUntrained(false)
            ->setKeyAbility('wisdom');
        $ride = new Skill();
        $ride
            ->setName('Ride')
            ->setShortname('ride')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('dexterity');
        $senseMotive = new Skill();
        $senseMotive
            ->setName('Sense Motive')
            ->setShortname('sense-motive')
            ->setKeyAbility('wisdom');
        $sleightOfHand = new Skill();
        $sleightOfHand
            ->setName('Sleight of Hand')
            ->setShortname('sleight-of-hand')
            ->setUntrained(false)
            ->setKeyAbility('dexterity');
        $spellcraft = new Skill();
        $spellcraft
            ->setName('Spellcraft')
            ->setShortname('spellcraft')
            ->setUntrained(false)
            ->setKeyAbility('intelligence');
        $stealth = new Skill();
        $stealth
            ->setName('Stealth')
            ->setShortname('stealth')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('dexterity');
        $survival = new Skill();
        $survival
            ->setName('Survival')
            ->setShortname('survival')
            ->setKeyAbility('wisdom');
        $swim = new Skill();
        $swim
            ->setName('Swim')
            ->setShortname('swim')
            ->setArmorCheckPenalty(true)
            ->setKeyAbility('strength');
        $useMagicDevice = new Skill();
        $useMagicDevice
            ->setName('Use Magic Device')
            ->setShortname('use-magic-device')
            ->setUntrained(false)
            ->setKeyAbility('charisma');
        $manager->persist($acrobatics);
        $this->setReference('acrobatics', $acrobatics);
        $manager->persist($appraise);
        $this->setReference('appraise', $appraise);
        $manager->persist($bluff);
        $this->setReference('bluff', $bluff);
        $manager->persist($climb);
        $this->setReference('climb', $climb);
        $manager->persist($craft);
        $this->setReference('craft', $craft);
        $manager->persist($diplomacy);
        $this->setReference('diplomacy', $diplomacy);
        $manager->persist($disableDevice);
        $this->setReference('disableDevice', $disableDevice);
        $manager->persist($disguise);
        $this->setReference('disguise', $disguise);
        $manager->persist($escapeArtist);
        $this->setReference('escapeArtist', $escapeArtist);
        $manager->persist($fly);
        $this->setReference('fly', $fly);
        $manager->persist($handleAnimal);
        $this->setReference('handleAnimal', $handleAnimal);
        $manager->persist($heal);
        $this->setReference('heal', $heal);
        $manager->persist($intimidate);
        $this->setReference('intimidate', $intimidate);
        $manager->persist($knowledgeArcana);
        $this->setReference('knowledgeArcana', $knowledgeArcana);
        $manager->persist($knowledgeDungeoneering);
        $this->setReference('knowledgeDungeoneering', $knowledgeDungeoneering);
        $manager->persist($knowledgeEngineering);
        $this->setReference('knowledgeEngineering', $knowledgeEngineering);
        $manager->persist($knowledgeGeography);
        $this->setReference('knowledgeGeography', $knowledgeGeography);
        $manager->persist($knowledgeHistory);
        $this->setReference('knowledgeHistory', $knowledgeHistory);
        $manager->persist($knowledgeLocal);
        $this->setReference('knowledgeLocal', $knowledgeLocal);
        $manager->persist($knowledgeNature);
        $this->setReference('knowledgeNature', $knowledgeNature);
        $manager->persist($knowledgeNobility);
        $this->setReference('knowledgeNobility', $knowledgeNobility);
        $manager->persist($knowledgePlanes);
        $this->setReference('knowledgePlanes', $knowledgePlanes);
        $manager->persist($knowledgeReligion);
        $this->setReference('knowledgeReligion', $knowledgeReligion);
        $manager->persist($linguistics);
        $this->setReference('linguistics', $linguistics);
        $manager->persist($perception);
        $this->setReference('perception', $perception);
        $manager->persist($perform);
        $this->setReference('perform', $perform);
        $manager->persist($profession);
        $this->setReference('profession', $profession);
        $manager->persist($ride);
        $this->setReference('ride', $ride);
        $manager->persist($senseMotive);
        $this->setReference('senseMotive', $senseMotive);
        $manager->persist($sleightOfHand);
        $this->setReference('sleightOfHand', $sleightOfHand);
        $manager->persist($spellcraft);
        $this->setReference('spellcraft', $spellcraft);
        $manager->persist($stealth);
        $this->setReference('stealth', $stealth);
        $manager->persist($survival);
        $this->setReference('survival', $survival);
        $manager->persist($swim);
        $this->setReference('swim', $swim);
        $manager->persist($useMagicDevice);
        $this->setReference('useMagicDevice', $useMagicDevice);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 4;
    }
}