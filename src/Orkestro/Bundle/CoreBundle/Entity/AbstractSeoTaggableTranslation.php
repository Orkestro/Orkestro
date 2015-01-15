<?php

namespace Orkestro\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractSeoTaggableTranslation extends AbstractTranslation
{
    /**
     * @ORM\Column(name="meta_title", type="text", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(name="meta_keywords", type="text", nullable=true)
     */
    protected $metaKeywords;


    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return AbstractSeoTaggableTranslation
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get meta title
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return AbstractSeoTaggableTranslation
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return AbstractSeoTaggableTranslation
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
}
