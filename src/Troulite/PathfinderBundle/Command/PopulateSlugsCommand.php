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

namespace Troulite\PathfinderBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Troulite\PathfinderBundle\Entity\Traits\Power;

class PopulateSlugsCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('pathfinder:slugs');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classes = ['Item'];

        foreach ($classes as $class) {
            $this->generateSlugs($class, $output);
        }
    }

    /**
     * @param $class
     * @param OutputInterface $output
     */
    private function generateSlugs($class, OutputInterface $output)
    {
        $qb = $this->em->getRepository("TroulitePathfinderBundle:" . $class)->createQueryBuilder('e');
        $iterator = $qb->getQuery()->iterate();

        $batchSize = 20;
        $i         = 0;

        foreach ($iterator as $row) {
            /** @var Power $entity */
            $entity = $row[0];

            if (!$entity->getSlug()) {
                $entity->setSlug($entity->getName());
            } else {
                $entity->setSlug(null);
            }

            // Memory cleanup
            if (($i % $batchSize) == 0) {
                $this->em->flush();
                $this->em->clear();
            }
            ++$i;
        }

        $this->em->flush();
    }
}