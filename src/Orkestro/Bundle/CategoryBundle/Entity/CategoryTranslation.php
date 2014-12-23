<?php

namespace Orkestro\Bundle\CategoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\CoreBundle\Entity\AbstractSeoTaggableTranslation;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="orkestro_category_translation")
 * @ORM\Entity
 */
class CategoryTranslation extends AbstractSeoTaggableTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\CategoryBundle\Entity\Category")
     */
    protected $translatable;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;


    /**
     * Set title
     *
     * @param string $title
     * @return CategoryTranslation
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
     * Set url
     *
     * @param string $url
     * @return CategoryTranslation
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