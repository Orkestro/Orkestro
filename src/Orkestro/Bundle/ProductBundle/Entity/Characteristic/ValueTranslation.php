<?php

namespace Orkestro\Bundle\ProductBundle\Entity\Characteristic;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="orkestro_product_characteristic_value_translation")
 * @ORM\Entity
 */
class ValueTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\Value")
     */
    protected $translatable;

    /**
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


    /**
     * Set value
     *
     * @param string $value
     * @return ValueTranslation
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
