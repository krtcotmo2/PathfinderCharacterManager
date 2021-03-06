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
use Troulite\PathfinderBundle\Entity\Weapon;

/**
 * Class LoadWeaponData
 *
 * @package Troulite\PathfinderBundle\DataFixtures\ORM\Base
 */
class LoadWeaponData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $longbow = new Weapon();
        $longbow
            ->setName('Longbow')
            ->setCategory('martial')
            ->setType('longbow')
            ->setCost(75)
            ->setCriticalRange(20)
            ->setCritical(3)
            ->setDamages('1d8')
            ->setShortDescription(
                'At almost 5 feet in height, a longbow is made up of one solid piece of carefully curved wood.'
            )
            ->setRange(20)
            ->setTwoHanded(true)
            ->setWeight(1.5);

        $manager->persist($longbow);
        $manager->flush();
        $this->setReference('longbow', $longbow);

        $longbow2 = new Weapon();
        $longbow2
            ->setName('Longbow +2')
            ->setCategory('martial')
            ->setType('longbow')
            ->setCost(75)
            ->setCritical(3)
            ->setCriticalRange(20)
            ->setDamages('1d8')
            ->setShortDescription(
                'At almost 5 feet in height, a longbow is made up of one solid piece of carefully curved wood.'
            )
            ->setRange(20)
            ->setTwoHanded(true)
            ->setWeight(1.5)
            ->addPower($this->getReference('ranged-weapon-power-enhancement-2'));

        $manager->persist($longbow2);
        $manager->flush();

        $this->setReference('longbow +2', $longbow2);

        $weapon = new Weapon();
        $weapon
            ->setName('Longsword + 1')
            ->setCategory('martial')
            ->setType('longsword')
            ->setCost(15)
            ->setCriticalRange(19)
            ->setCritical(2)
            ->setDamages('1d8')
            ->setShortDescription(
                'A longsword (also spelled long sword, long-sword) is a type of sword characterized as having a cruciform hilt with a grip for two handed use and a straight double-edged blade of around 100–122 cm (39–48 in).'
            )
            ->setRange(0)
            ->setTwoHanded(false)
            ->setWeight(2)
            ->addPower($this->getReference('melee-weapon-power-enhancement-1'));

        $manager->persist($weapon);
        $manager->flush();
        $this->setReference('longsword +1', $weapon);

        $weapon = new Weapon();
        $weapon
            ->setName('La Rudement Grande')
            ->setCategory('martial')
            ->setType('greatsword')
            ->setCost(15)
            ->setCriticalRange(19)
            ->setCritical(2)
            ->setDamages('2d6')
            ->setRange(0)
            ->setTwoHanded(true)
            ->setWeight(2)
            ->addPower($this->getReference('melee-weapon-power-enhancement-5'));

        $manager->persist($weapon);
        $manager->flush();

        $weapon = new Weapon();
        $weapon
            ->setName('Whip +1')
            ->setCategory('exotic')
            ->setType('whip')
            ->setCost(1)
            ->setCriticalRange(20)
            ->setCritical(2)
            ->setDamages('1d3')
            ->setRange(0)
            ->setTwoHanded(false)
            ->setWeight(1)
            ->addPower($this->getReference('melee-weapon-power-enhancement-1'));

        $manager->persist($weapon);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 12;
    }
}