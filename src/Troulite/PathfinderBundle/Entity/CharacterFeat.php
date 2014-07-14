<?php

namespace Troulite\PathfinderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CharacterFeat
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CharacterFeat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="Level", inversedBy="feats")
     * @ORM\JoinColumn(name="level", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $level;

    /**
     * @var Feat
     *
     * @ORM\ManyToOne(targetEntity="Feat")
     * @ORM\JoinColumn(name="feat", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $feat;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active = false;

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
     * Set level
     *
     * @param Level $level
     *
     * @return CharacterFeat
     */
    public function setLevel(Level $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get character
     *
     * @return BaseCharacter
     */
    public function getCharacter()
    {
        return $this->level->getCharacter();
    }

    /**
     * Set feat
     *
     * @param Feat $feat
     *
     * @return CharacterFeat
     */
    public function setFeat(Feat $feat = null)
    {
        $this->feat = $feat;

        return $this;
    }

    /**
     * Get feat
     *
     * @return Feat
     */
    public function getFeat()
    {
        return $this->feat;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CharacterFeat
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    public function __toString()
    {
        return $this->getFeat()->getName();
    }
}
