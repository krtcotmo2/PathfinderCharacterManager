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

namespace Troulite\PathfinderBundle\Twig;


use Symfony\Component\Translation\DataCollectorTranslator;
use Troulite\PathfinderBundle\Entity\Character;
use Troulite\PathfinderBundle\Entity\Party;

class TranslationExtension extends \Twig_Extension
{
    private $mappings = [
        'spelleffects'     => 'spells',
        'powereffects'     => 'classpowers',
        'itempowereffects' => 'itempowers',
        'classdefinitions' => 'classes',
        'subclasss'        => 'classes',
    ];

    /**
     * @var DataCollectorTranslator
     */
    private $translator;

    /**
     * @param DataCollectorTranslator $translator
     */
    public function __construct(DataCollectorTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('pftrans', array($this, 'pftransFilter')),
        );
    }

    /**
     * Translates a property related to an entity guessing the domain automatically
     *
     * @param $entity
     * @param string $property
     * @param string $fallbackDomain
     * @param array $params
     * @param null $locale
     *
     * @return string
     */
    public function pftransFilter($entity, $property = 'name', $fallbackDomain = 'messages', $params = [], $locale = null)
    {
        if (!is_object($entity)) {
            return $this->translator->trans($entity, $params, $fallbackDomain, $locale);;
        }
        if ($entity instanceof Character || $entity instanceof Party) {
            return $entity->__toString();
        }

        $class = get_class($entity);
        for ($classes[] = $class; $class = get_parent_class($class); $classes[] = $class) {
            ;
        }
        $entityClass = explode('\\', $classes[count($classes) - 1]);
        $domain = strtolower($entityClass[count($entityClass) - 1]) . 's';

        if (array_key_exists($domain, $this->mappings)) {
            $domain = $this->mappings[$domain];
        }

        return $this->translator->trans($entity . '.' . $property, $params, $domain, $locale);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translation_extension';
    }
}