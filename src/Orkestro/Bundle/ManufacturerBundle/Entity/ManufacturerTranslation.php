<?php

namespace Orkestro\Bundle\ManufacturerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\CoreBundle\Entity\AbstractSeoTaggableTranslation;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @ORM\Table(name="orkestro_manufacturer_translation")
 * @ORM\Entity
 */
class ManufacturerTranslation extends AbstractSeoTaggableTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ManufacturerBundle\Entity\Manufacturer")
     */
    protected $translatable;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="short_description", type="text")
     */
    private $shortDescription;

    /**
     * @ORM\Column(name="full_description", type="text")
     */
    private $fullDescription;


    /**
     * Set title
     *
     * @param string $title
     * @return ManufacturerTranslation
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

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return ManufacturerTranslation
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set fullDescription
     *
     * @param string $fullDescription
     * @return ManufacturerTranslation
     */
    public function setFullDescription($fullDescription)
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    /**
     * Get fullDescription
     *
     * @return string 
     */
    public function getFullDescription()
    {
        return $this->fullDescription;
    }
}
