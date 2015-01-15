<?php

namespace Orkestro\Bundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="orkestro_product_kind_translation")
 * @ORM\Entity
 */
class ProductKindTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ProductBundle\Entity\ProductKind")
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
     * @return ProductKindTranslation
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
