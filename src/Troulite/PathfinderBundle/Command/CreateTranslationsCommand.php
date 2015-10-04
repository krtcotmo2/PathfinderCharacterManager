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

class CreateTranslationsCommand extends ContainerAwareCommand
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
            ->setName('pathfinder:translations:create');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classes = ['Condition'];

        foreach ($classes as $class) {
            $this->createTranslations($class, $output);
        }
    }

    /**
     * @param $class
     * @param OutputInterface $output
     */
    private function createTranslations($class, OutputInterface $output)
    {
        $lang = 'en';
        $qb = $this->em->getRepository("TroulitePathfinderBundle:" . $class)->createQueryBuilder('e');
        $iterator = $qb->getQuery()->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        )->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $lang
        )->iterate();

        $batchSize = 20;
        $i         = 0;

        $en = fopen('/tmp/' . strtolower($class) . 's.'.$lang.'.xlf', 'w');

        fputs($en, <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
    <file source-language="en" target-language="{$lang}" datatype="plaintext" original="not.available">
        <body>

EOT
);

        foreach ($iterator as $row) {
            /** @var Power $entity */
            $entity = $row[0];

            $slug = $entity->getName();
            $md5name = md5($slug . '.name');
            $md5shortdesc = md5($slug . '.shortdesc');
            $md5longdesc = md5($slug . '.longdesc');

            $xml = '';

            if ($entity->getName()) {
                $xml .= <<<EOT
            <trans-unit id="$md5name" resname="{$slug}.name">
                <source>{$slug}.name</source>
                <target><![CDATA[{$entity->getName()}]]></target>
            </trans-unit>

EOT;
            }
            if ($entity->getShortDescription()) {
                $xml .= <<<EOT
            <trans-unit id="$md5shortdesc" resname="{$slug}.shortdesc">
                    <source>{$slug}.shortdesc</source>
                    <target><![CDATA[{$entity->getShortDescription()}]]></target>
            </trans-unit>

EOT;
            }
            if ($entity->getLongDescription()) {
                $xml .= <<<EOT
            <trans-unit id="$md5longdesc" resname="{$slug}.longdesc">
                    <source>{$slug}.longdesc</source>
                    <target><![CDATA[{$entity->getLongDescription()}]]></target>
            </trans-unit>

EOT;
            }

            fputs($en, $xml);

            // Memory cleanup
            if (($i % $batchSize) == 0) {
                $this->em->clear();
            }
            ++$i;
        }

        $this->em->clear();

        fputs($en, <<<EOT
        </body>
    </file>
</xliff>
EOT
        );

        fclose($en);
    }
}