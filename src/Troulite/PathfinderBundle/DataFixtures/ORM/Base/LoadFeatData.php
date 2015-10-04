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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Troulite\PathfinderBundle\Entity\Feat;

/**
 * Class LoadFeatData
 *
 * @package Troulite\PathfinderBundle\DataFixtures\ORM\Base
 */
class LoadFeatData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $finder = new Finder();
        $finder->files()->in('src/Troulite/PathfinderBundle/Resources/data/')->name('dons.csv');
        /** @var $file SplFileInfo */
        foreach($finder as $file) {
            $handle = fopen($file->getRealPath(), 'r');
            $header = null;
            while (($row = fgetcsv($handle, null, ',', '"', "\\")) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data = array_combine($header, $row);
                    if (!in_array($data['source'], array('PFRPG Core', "Advanced Player's Guide", 'Bestiary'))) {
                        continue;
                    }

                    $feat = new Feat();
                    $feat
                        ->setName($data['name'])
                        ->setShortDescription($data['description'])
                        ->setLongDescription($data['benefit'])
                        ->setPassive($data['passive']);
                    if (array_key_exists('effects', $data) && $data['effects']) {
                        $feat->setEffects(json_decode($data['effects']));
                    }
                    if (array_key_exists('conditions', $data) && $data['conditions']) {
                        $feat->setConditions(json_decode($data['conditions']));
                    }
                    if (array_key_exists('external_conditions', $data) && $data['external_conditions']) {
                        $feat->setExternalConditions(json_decode($data['external_conditions']));
                    }
                    $manager->persist($feat);

                    $this->addReference('feat - ' . $feat->getName(), $feat);
                }
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 5;
    }
}