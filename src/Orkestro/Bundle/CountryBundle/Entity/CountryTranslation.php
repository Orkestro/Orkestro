<?php

namespace Orkestro\Bundle\CountryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\CoreBundle\Entity\AbstractSeoTaggableTranslation;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @ORM\Table(name="orkestro_country_translation")
 * @ORM\Entity
 */
class CountryTranslation extends AbstractSeoTaggableTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\CountryBundle\Entity\Country")
     */
    protected $translatable;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;


    /**
     * Set title
     *
     * @param string $title
     * @return CountryTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}