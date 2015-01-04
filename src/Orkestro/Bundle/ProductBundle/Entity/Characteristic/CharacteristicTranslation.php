<?php

namespace Orkestro\Bundle\ProductBundle\Entity\Characteristic;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="orkestro_product_characteristic_translation")
 * @ORM\Entity
 */
class CharacteristicTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic")
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
     * @return CharacteristicTranslation
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
