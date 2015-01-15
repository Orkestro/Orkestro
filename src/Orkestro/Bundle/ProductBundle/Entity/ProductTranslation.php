<?php

namespace Orkestro\Bundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\CoreBundle\Entity\AbstractSeoTaggableTranslation;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @ORM\Table(name="orkestro_product_translation")
 * @ORM\Entity
 */
class ProductTranslation extends AbstractSeoTaggableTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Product")
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;


    /**
     * Set title
     *
     * @param string $title
     * @return ProductTranslation
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
     * @return ProductTranslation
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
     * @return ProductTranslation
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

    /**
     * Set url
     *
     * @param string $url
     * @return ProductTranslation
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
